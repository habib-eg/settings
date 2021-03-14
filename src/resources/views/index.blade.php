@extends('dashboard::layouts.layout')
@push('header')
    <div class="header bg-primary pb-6">
        <div class="container-fluid">
            <div class="header-body">
                <div class="row align-items-center py-4">
                    <div class="col-lg-6 col-7">
                        <x-dashboard.breadcrumb-component>
                            <li class="breadcrumb-item active" aria-current="page">@lang('main.setting')</li>
                        </x-dashboard.breadcrumb-component>
                    </div>
                </div>
                >
            </div>
        </div>
    </div>
@endpush
@section('content')
    <
    <div class="card">
        <div class="card-body">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">@lang('settings::settings.close')</span>
                </button>

                <form action="{{route('dashboard.clear.cache')}}"
                      method="post">@csrf
                    @lang('settings::settings.settings_message')
                    <button class="btn btn-sm btn-primary inline-block">@lang('main.force_now')</button>
                </form>
            </div>
            <h4 class="card-title">@lang('settings::settings.filter')</h4>

            <form method="GET" class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name">@lang('settings::settings.name')</label>
                        <input type="text" class="form-control" name="name" id="name" value="{{request('name')}}"
                               placeholder="@lang('settings::settings.name')"/>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="value">@lang('settings::settings.value')</label>
                        <input type="text" class="form-control" name="value" id="value" value="{{request('value')}}"
                               placeholder="@lang('settings::settings.value')"/>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="locale">@lang('settings::settings.locale')</label>
                        <select class="form-control" name="locale" id="locale">
                            <option value="">@lang('settings::settings.select')</option>
                            @foreach (config('app.locales',[]) as $locales)
                                <option
                                    value="{{$locales}}" {{$locales==request('locale')?'selected':''}}>{{$locales}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="locale">@lang('main.group_by')</label>
                        <select class="form-control" name="group_by" id="group_by">
                            <option value="">@lang('settings::settings.select')</option>
                            @foreach ($headers as $header)
                                <option
                                    value="{{$header ?? 'others'}}" {{($header==request('group_by')  || ($loop->index==0 && !request()->has('group_by')))?' selected ':''}}>{{$header ?? 'others'}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">@lang('settings::settings.filter')</button>
                </div>
            </form>
        </div>
    </div>
    <div class="col-md-12">
        <div class="nav-wrapper ">
            <ul class="nav nav-pills nav-fill flex-column flex-md-row" id="tabs-icons-text" role="tablist">
                @foreach ($headers as $header)
                    <li class="nav-item">
                        <a class="nav-link mb-sm-3 mb-md-0 {{(request('group_by') === $header || ($loop->index==0 && !request()->has('group_by')))?' active ':''}}"
                           id="tabs-icons-text-{{$loop->index}}-tab"
                           data-toggle="tab"
                           href="#tabs-icons-text-{{$loop->index}}"
                           role="tab"
                           aria-controls="tabs-icons-text-{{$loop->index}}"
                            {{request('group_by') === $header?' aria-selected="true" ':''}}><i
                                class="ni ni-cloud-upload-96 mr-2"></i>{{$header}}</a>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="card shadow">
            <div class="card-body">
                <div class="tab-content w-100" id="myTabContent">
                    @foreach ($headers as $header)
                        <div
                            class="tab-pane fade w-100 {{(request('group_by') === $header  || ($loop->index==0 && !request()->has('group_by'))) ?' show active ':''}} "
                            id="tabs-icons-text-{{$loop->index}}" role="tabpanel"
                            aria-labelledby="tabs-icons-text-{{$loop->index}}-tab">
                            <div class="row">
                                @foreach ($settings->where('group_by',$header!='others' ? $header : null) as $setting)
                                    <div class="col-sm-4 p-1">
                                        <div class="card">
                                            <div class="card-body">
                                                <h4 class="card-title">{{ucwords(str_replace('_',' ',$setting->name))}}
                                                    ({{$setting->locale}})</h4>
                                                <form action="{{route('dashboard.setting.update',$setting)}}"
                                                      method="post"
                                                      @if ($setting->type=='file') enctype="multipart/form-data" @endif >@csrf @method('PUT')
                                                    {{$setting->type ?? 'no type here'}}
                                                    @if ($setting->type=='text')
                                                        <div class="form-group">
                                                            <textarea class="form-control" required min="1" name="value"
                                                                      placeholder="{{$setting->value}}">{{old('settings.'.$setting->id.'.value',$setting->value)}}</textarea>
                                                        </div>
                                                    @elseif ($setting->type=='number')
                                                        <div class="form-group">
                                                            <input type="number" step="any" class="form-control"
                                                                   name="value"
                                                                   value="{{old('settings.'.$setting->id.'.value',$setting->value)}}"
                                                                   placeholder="{{$setting->value}}"/>
                                                        </div>
                                                    @elseif ($setting->type=='file')
                                                        <div class="form-group">
                                                            <a href="{{url($setting->value)}}"
                                                               target="_blank">@lang('settings::settings.view')</a>
                                                            <input type="file" step="any" class="form-control"
                                                                   name="value"
                                                                   value="{{old('settings.'.$setting->id.'.value',$setting->value)}}"
                                                                   placeholder="{{$setting->value}}"/>
                                                        </div>
                                                    @else
                                                        <div class="form-group">
                                                            <input type="text" required min="1" class="form-control"
                                                                   name="value"
                                                                   value="{{old('settings.'.$setting->id.'.value',$setting->value)}}"
                                                                   placeholder="{{$setting->value}}"/>
                                                        </div>
                                                    @endif
                                                    <button type="submit"
                                                            class="btn btn-warning">@lang('main.update')</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-sm-12">
            {{--            {{$settings->withQueryString()->links()}}--}}
        </div>
    </div>
@stop
