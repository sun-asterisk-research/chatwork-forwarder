@extends('layout.app')
@section('content')
<!-- Datatables Header -->
<div class="content-header">
    <div class="header-section">
        <h1>
            Datatables<br><small>HTML tables can become fully dynamic with cool features!</small>
        </h1>
    </div>
</div>
<ul class="breadcrumb breadcrumb-top">
    <li>Tables</li>
    <li><a href="">Datatables</a></li>
</ul>
<!-- END Datatables Header -->

<!-- Datatables Content -->
<div class="block full">
    <div class="block-title">
        <h2><strong>Datatables</strong> integration</h2>
    </div>
    <p><a href="https://datatables.net/" target="_blank">DataTables</a> is a plug-in for the Jquery Javascript library. It is a highly flexible tool, based upon the foundations of progressive enhancement, which will add advanced interaction controls to any HTML table. It is integrated with template's design and it offers many features such as on-the-fly filtering and variable length pagination.</p>

    <div class="table-responsive">
        <table id="example-datatable" class="table table-vcenter table-condensed table-bordered">
            <thead>
                <tr>
                    <th class="text-center">ID</th>
                    <th class="text-center"><i class="gi gi-user"></i></th>
                    <th>Client</th>
                    <th>Email</th>
                    <th>Subscription</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">1</td>
                    <td class="text-center"><img src="img/avatar2.jpg" alt="avatar" class="img-circle"></td>
                    <td><a href="javascript:void(0)">client1</a></td>
                    <td>client1@company.com</td>
                    <td><span class="label label-primary">Personal</span></td>
                    <td class="text-center">
                        <div class="btn-group">
                            <a href="javascript:void(0)" data-toggle="tooltip" title="Edit" class="btn btn-xs btn-default"><i class="fa fa-pencil"></i></a>
                            <a href="javascript:void(0)" data-toggle="tooltip" title="Delete" class="btn btn-xs btn-danger"><i class="fa fa-times"></i></a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="text-center">58</td>
                    <td class="text-center"><img src="img/avatar2.jpg" alt="avatar" class="img-circle"></td>
                    <td><a href="javascript:void(0)">client58</a></td>
                    <td>client58@company.com</td>
                    <td><span class="label label-primary">Personal</span></td>
                    <td class="text-center">
                        <div class="btn-group">
                            <a href="javascript:void(0)" data-toggle="tooltip" title="Edit" class="btn btn-xs btn-default"><i class="fa fa-pencil"></i></a>
                            <a href="javascript:void(0)" data-toggle="tooltip" title="Delete" class="btn btn-xs btn-danger"><i class="fa fa-times"></i></a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="text-center">59</td>
                    <td class="text-center"><img src="img/avatar2.jpg" alt="avatar" class="img-circle"></td>
                    <td><a href="javascript:void(0)">client59</a></td>
                    <td>client59@company.com</td>
                    <td><span class="label label-primary">Personal</span></td>
                    <td class="text-center">
                        <div class="btn-group">
                            <a href="javascript:void(0)" data-toggle="tooltip" title="Edit" class="btn btn-xs btn-default"><i class="fa fa-pencil"></i></a>
                            <a href="javascript:void(0)" data-toggle="tooltip" title="Delete" class="btn btn-xs btn-danger"><i class="fa fa-times"></i></a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="text-center">60</td>
                    <td class="text-center"><img src="img/avatar2.jpg" alt="avatar" class="img-circle"></td>
                    <td><a href="javascript:void(0)">client60</a></td>
                    <td>client60@company.com</td>
                    <td><span class="label label-info">Business</span></td>
                    <td class="text-center">
                        <div class="btn-group">
                            <a href="javascript:void(0)" data-toggle="tooltip" title="Edit" class="btn btn-xs btn-default"><i class="fa fa-pencil"></i></a>
                            <a href="javascript:void(0)" data-toggle="tooltip" title="Delete" class="btn btn-xs btn-danger"><i class="fa fa-times"></i></a>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<!-- END Datatables Content -->
@endsection
