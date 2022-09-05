@extends('layouts.plantilla')
@section('title','Proyectos')
@section ('content')

<div class="container-fluid p-0">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title">{{$direccion}}</h4>
                    </div>
                    <div class="tabla-responsiva">
                        <table id="table" class="display table2" cellspacing="0" width="100%">
                            <thead id="thead-table">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Area</th>
                                    <th>Estado</th>
                                    <th>Creación</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Area</th>
                                    <th>Estado</th>
                                    <th>Creación</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                            <tbody>
                                @foreach($proyectos as $key => $proyecto)     
                                <tr id="{{$proyecto->id}}" class="border-bottom details-control">
                                  <td>{{$proyecto->id}}</td>
                                  <td class="td-short">{{$proyecto->nombre}}</td>
                                  <td class="td-short">{{$proyecto->descripcion}}</td>
                                  <td class="td-short">{{$proyecto->area}}</td>
                                  <td>{{$proyecto->estado}}</td>
                                  <td>{{$proyecto->created_at}}</td>
                                <td>
                                    <a href="#" onclick="openiframe('Editar documento','{{ route('archivos.update',$proyecto)}}')" class="btn btn-link text-{{$bg->customcolor}} p-0 mb-0" data-toggle="tooltip" data-placement="top" title="" data-original-title="Editar"><i data-feather="edit" class="mr-1 text-md"></i></a>
                                    <a href="{{ route('archivos.destroy',$proyecto) }}" onclick="return confirm('Estás seguro que deseas eliminar el registro?');" class="btn btn-link text-danger p-0 mb-0 ml-4" href="javascript:;" data-toggle="tooltip" data-placement="top" title="" data-original-title="Eliminar"><i data-feather="trash" class="mr-1 text-md"></i></a></td>
                            </tr>
                            @endforeach    
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>    
</div>
</div>

<script type="text/javascript" src="{{ URL::asset('js/app.js') }}"></script>
<script>
    function format(d) {
        return '<div>Hidden Value: ' + d + '</div>';
    }

    $(document).ready(function(){
    $('#thead-table').addClass('bg-none');

    editorTab = $('#table').DataTable({
        initComplete: function () {
            this.api()
                .columns()
                .every(function () {
                    var column = this;
                    if((column.slice(0, 1).shift()) == 3 || (column.slice(0, 1).shift()) == 4){
                        var element = "custom-select bg-ghost form-control form-control-sm";
                    }else{
                        var element = "custom-select bg-ghost form-control form-control-sm invisible";
                    }
                    if((column.slice(0, 1).shift()) != 4){
                        var text = "Filtrar columna";
                    }else{
                        var text = "Filtrar";
                    }
                    var select = $('<select class="'+element+'"><option value="">'+text+'</option></select>')
                        .appendTo($(column.footer()).empty())
                        .on('change', function () {
                            var val = $.fn.dataTable.util.escapeRegex($(this).val());
                            column.search(val ? '^' + val + '$' : '', true, false).draw();
                        });
 
                    column.data().unique().sort()
                        .each(function (d, j) {
                            select.append('<option value="' + d + '">' + d + '</option>');
                        });
                });
            },
            "language": {
                "lengthMenu": "Registros por página _MENU_",
                "zeroRecords": "No se encontraron registros",
                "info": "Página  _PAGE_ de _PAGES_",
                "infoEmpty": "Sin registros",
                "infoFiltered": "(Filtardo de _MAX_ total registros)",
                'search':'Buscar:',
                'paginate':{
                'next':'Siguiente',
                'previous':'Anterior'
            }
            },
        });

        $('#table').on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var row = table.row(tr);
     
            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
            } else {
                // Open this row
                row.child(format(row.data())).show();
                tr.addClass('shown');
            }
        });

    });
    
</script>

@endsection
