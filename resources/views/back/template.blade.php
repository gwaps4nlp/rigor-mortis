@extends('back.master')

@section('css')

<style>
.herbe{display:none;}
h1{
  font-size:24px;
}
ul.nav > li > a {
  padding: 5px 0px;
}
ul.nav > li > ul > li > a {
  padding: 5px 15px;
}
</style>
@yield('style')

@stop

@section('container')

<div class="row">
	<div class="col-2">
    <ul class="nav flex-column" role="tablist" aria-multiselectable="true">
      <li>{!! link_to('upl/admin-index','UPL') !!}</li>
      <li>{!! link_to('faq/admin-index','FAQ') !!}</li>
      <li>{!! link_to('news',trans('back/index.news')) !!}</li>
      <li>{!! link_to('user/index-admin',trans('back/index.users')) !!}</li>
      <li>{!! link_to('admin/reporting',trans('back/index.reporting')) !!}</li>
      <li>{!! link_to('translation',trans('back/index.translations')) !!}</li>
      <li>{!! link_to('language',trans('back/index.language')) !!}</li>
    </ul>
  </div>
  <div class="col-10">
    @yield('content')
  </div>
</div>

@stop