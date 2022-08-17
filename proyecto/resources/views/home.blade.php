 @if (auth::user())
 @if (auth::user()->iactivo !=1 )
 @php
 header("Location: " . URL::to('/'), true, 302);
 exit();
 @endphp
 @endif
 @else
 @php
 header("Location: " . URL::to('/'), true, 302);
 exit();
 @endphp
 @endif
 @extends('layouts.plantilla')

 @section('content')
 <div class="row">
     <div class="col-lg-12 col-md-12 col-sm-12">
         <div class="card">
             <div class="card-body">
                 <div class="d-flex justify-content-between">
                     <h4 class="card-title">Mis proyectos</h4>
                     <div>
                         <a class="mr-3 pointer d-none d-sm-none d-md-inline-flex" onclick="$('#dataTab').DataTable().ajax.reload();"><i
                                 class="fa fa-refresh"></i></a>
                         <span class="dropdown">
                             <a class="btn btn-outline-dark btn-sm dropdown-toggle sel-text" href="#"
                                 data-toggle="dropdown" aria-expanded="true">Proyectos en curso</a>
                             <span class="dropdown-menu dropdown-menu-right" x-placement="bottom-end"
                                 style="position: absolute; transform: translate3d(-100px, 26px, 0px); top: 0px; left: 0px; will-change: transform;">
                                 <a href="#" class="dropdown-item sel-all">Todos</a>
                                 <a href="#" class="dropdown-item sel-pen">En curso</a>
                                 <a href="#" class="dropdown-item sel-fin">Finalizados</a>
                             </span></span>
                     </div>
                 </div>
                 <p class="mb-4">Presiona <span class="text-success text-bold">nuevo</span> para crear un proyecto. Para
                     <span class="text-info text-bold">editar</span>, <span
                         class="text-danger text-bold">eliminar</span> o <span
                         class="text-secondary text-bold">finalizar</span>, seleccione la fila <span
                         class="text-md">☑</span> y presione el botón correspondiente.</p>

                 <div class="table-responsive">
                     <table id="dataTab" class="display table" cellspacing="0" width="100%">
                         <thead>
                             <tr>
                                 <th></th>
                                 <th>Nombre</th>
                                 <th>Descripción</th>
                                 <th>Inicio</th>
                                 <th>Final</th>
                                 <th>Estado</th>
                                 <th>Tareas</th>
                                 <th>Info.</th>
                             </tr>
                         </thead>
                     </table>
                 </div>
             </div>
         </div>
     </div>
 </div>

<script type="text/javascript">
var editor;
var editorTab;
var url = '/SGPI/crud/proyecto';
var jsontype = 'application/json';
var token = $("meta[name='csrf-token']").attr('content');

function toask_error(e) {
    $(".preloader").fadeOut(400, function() {
        setTimeout(function() {
            toastr.options = {
                timeOut: 5000,
                progressBar: true,
                showMethod: "slideDown",
                hideMethod: "slideUp",
                showDuration: 200,
                hideDuration: 300,
                positionClass: "toast-bottom-center",
            };
            toastr.error("Operación no exitosa");
            a(".theme-switcher").removeClass("open")
        }, 500)
    });
    console.log(e);
}

function opengraphic(id) {    
    var url = '{{ route("proyectos.grafica", ":id") }}';
    url = url.replace(':id', id);
    var id = url.replace(/[^0-9]/ig, '');
    openiframe('Gráfica Gantt', url);
}

function opendocument(id) {    
    var url = '{{ route("proyectos.documentos", ":id") }}';
    url = url.replace(':id', id);
    var id = url.replace(/[^0-9]/ig, '');
    openiframe('Documentos', url);
}

$(document).ready(function() {
    let parentId;
    editor = new $.fn.dataTable.Editor({
        ajax: {
            create: {
                type: 'POST',
                url: url,
                contentType: jsontype,
                data: function(addData) {
                    addData.data[0]['_token'] = token;
                    addData.data[0]['index'] = 'save';
                    return JSON.stringify(addData.data[0]);
                },
                error: function(e) {
                    toask_error(e);
                },
                success: function(respuesta){
                    parentId = respuesta['data'][0].id;
                }
            },
            edit: {
                type: 'POST',
                url: url,
                contentType: jsontype,
                data: function(editData) {
                    var id = Object.keys(editData.data);
                    editData.data[id[0]]['_token'] = token;
                    editData.data[id[0]]['id'] = id[0];
                    editData.data[id[0]]['index'] = 'update';
                    return JSON.stringify(editData.data[id[0]]);
                },
                complete: function() {
                    editorTab.row({
                        selected: true
                    }).deselect();
                    editorTab.columns(5).search('En curso').draw();
                    $('#dataTab').DataTable().ajax.reload();
                },
                error: function(e) {
                    toask_error(e);
                }
            },
            remove: {
                type: 'POST',
                url: url,
                contentType: jsontype,
                data: function(editData) {
                    var idSelected;
                    $.each(editData.data, function(key, value) {
                        idSelected = value.id;
                    });
                    editData.data[idSelected]['_token'] = token;
                    editData.data[idSelected]['index'] = 'remove';
                    return JSON.stringify(editData.data[idSelected]);
                },
                error: function(e) {
                    toask_error(e);
                }
            }
        },

        table: '#dataTab',
        idSrc: 'id',
        fields: [{
                label: 'Estado:',
                name: 'estado',
                multiEditable: false
            },
            {
                label: 'Nombre:',
                name: 'nombre',
                multiEditable: false
            },
            {
                label: 'Descripción:',
                name: 'descripcion',
                multiEditable: false
            },
            {
                label: 'Fecha inicio:',
                name: 'inicio',
                type: 'datetime',
                multiEditable: false
            },
            {
                label: 'Fecha final:',
                name: 'final',
                type: 'datetime',
                multiEditable: false
            }
        ],
    });

    editor.on('initCreate', function() {
        editor.show();
        editor.hide('estado');
    });
    editor.on('initEdit', function() {
        editor.show();
        editor.hide('estado');
    });

    $('#dataTab').on('click', 'tbody td.row-add', function(e) {
        var url = '{{ route("tareas.create", ":id") }}';
        if(this.parentNode.id == null || this.parentNode.id == '') {
            this.parentNode.id = parentId;
        }
        url = url.replace(':id', this.parentNode.id);
        var id = url.replace(/[^0-9]/ig, '');
        openiframe('Crear nueva tarea', url);
    });

    function createChild(row) {
        var rowData = row.data();

        var aFecha0 = new Date().toJSON().slice(0,10).replace(/-/g,'/').split('/');
        var aFecha1 = rowData.inicio.split('-');
        var aFecha2 = rowData.final.split('-');
        var fFecha0 = Date.UTC(aFecha0[0],aFecha0[1]-1,aFecha0[2]);
        var fFecha1 = Date.UTC(aFecha1[0],aFecha1[1]-1,aFecha1[2]);
        var fFecha2 = Date.UTC(aFecha2[0],aFecha2[1]-1,aFecha2[2]);
        var dif = fFecha2 - fFecha1;
        var dif2 = fFecha0 - fFecha2;
        var dias = Math.floor(dif / (1000 * 60 * 60 * 24))+1;
        var dias2 = rowData.estado == 'En curso' ? Math.floor(dif2 / (1000 * 60 * 60 * 24)) : 0;
        var view = 'http://192.168.10.103/SGPI/grafica/proyecto/'+rowData.id;
        var table = '<table class="table m-0" border="0" style="max-with: 100%">' +
            '<tr>' +
            '<td style="width: 10%;"> Nombre:' +
            '</td>' +
            '<td class="td-large" style="width: 90%;">' +
            rowData.nombre +
            '</td>' +
            '</tr>' +
            '<tr>' +
            '<td style="width: 10%;"> Descripción:' +
            '</td>' +
            '<td class="td-large" style="width: 90%;">' +
            rowData.descripcion +
            '</td>' +
            '</tr>' +
            '<tr>' +
            '<td style="width: 10%;"> Duración:' +
            '</td>' +
            '<td class="td-large" style="width: 90%;">' +
            dias + ' días <span class="text-danger text-xs">' + (dias2 > 0 ? "(Finalización excedida por " + dias2 + " días)" : "") +
            '</span></td>' +
            '</tr>' +
            '<tr>' +
            '<td style="width: 10%;"> Creador:' +
            '</td>' +
            '<td class="td-large" style="width: 90%;">' +
            rowData.id_user +
            '</td>' +
            '</tr>' +
            '<tr>' +
            '<td style="width: 10%;"> Estado:' +
            '</td>' +
            '<td style="width: 90%;"><div class="progress bg-gainsboro"><div class="progress-bar bg-secondary" role="progressbar" style="width: 51%;" aria-valuenow="42" aria-valuemin="0" aria-valuemax="100">42%</div></div>' +
            '</td>' +
            '</tr>' +
            '<tr>' +
            '<td style="width: 10%;"> ' +
            '</td>' +
            '<td style="width: 90%;"><button class=" btn btn-success btn-sm mr-1" type="button" onclick="opengraphic('+rowData.id+');">Gráfica gantt</button><button class=" btn btn-info btn-sm mr-1" type="button" onclick="opendocument('+rowData.id+');">Añadir documento</button>' +
            '</td>' +
            '</tr>' +
            '</table>'
        row.child(table).show();
    }

    function destroyChild(row) {
        var table = $('dataTab', row.child());
        table.detach();
        table.DataTable().destroy();
        row.child.hide();
    }

    $('#dataTab').on('click', 'tbody td.dt-control', function() {
        var tr = $(this).closest('tr');
        var row = editorTab.row(tr);
        if (row.child.isShown()) {
            destroyChild(row);
            tr.removeClass('shown');
        } else {
            createChild(row);
            tr.addClass('shown');
            $('.shown').next().addClass('bg-ghost');
        }
    });

    editorTab = $('#dataTab').DataTable({
        dom: 'Bfrtip',
        idSrc: 'id',
        ajax: {
            type: 'POST',
            url: url,
            data: {
                '_token': token,
                'index': 'load',
            },
            complete: function(json) {
                $('.select-checkbox, .row-add, .row-view').addClass('pointer');
                $('td').addClass('td-short');
                editorTab.columns(5).search('En curso').draw();
            },
            error: function(e) {
                toask_error(e);
            }
        },
        columns: [{
                data: null,
                defaultContent: '',
                className: 'select-checkbox',
                orderable: false
            },
            {
                data: 'nombre'
            },
            {
                data: 'descripcion'
            },
            {
                data: 'inicio'
            },
            {
                data: 'final',
                className: 'text-secondary'
            },
            {
                data: 'estado'
            },
            {
                data: null,
                defaultContent: '<i class="fa fa-plus"/>',
                className: 'row-add dt-center',
                orderable: false
            },
            {
                data: null,
                defaultContent: '',
                className: 'dt-control dt-center',
                orderable: false
            }
        ],
        lengthMenu: [
            [10]
        ],
        order: [4, 'asc'],
        select: {
            style: 'os',
            selector: 'td:first-child'
        },
        language: lenguaje,
        buttons: [{
                extend: 'create',
                text: 'Nuevo',
                editor: editor
            },
            {
                extend: 'edit',
                text: 'Editar',
                editor: editor
            },
            {
                extend: 'remove',
                text: 'Eliminar',
                editor: editor
            },
            {
                extend: 'selectedSingle',
                text: 'Finalizar',
                className: 'buttons-finish',
                action: function(e, dt, node, config) {
                    console.log(editorTab.row({
                        selected: true
                    }).index());
                    editor.edit(editorTab.row({
                        selected: true
                    }).index(), false).set('estado', 'Finalizado').submit();
                }
            }
        ]
    });
    $('.buttons-create').removeClass('dt-button').addClass('btn btn-success btn-sm');
    $('.buttons-edit').removeClass('dt-button').addClass('btn btn-info btn-sm');
    $('.buttons-remove').removeClass('dt-button').addClass('btn btn-danger btn-sm');
    $('.buttons-finish').removeClass('dt-button').addClass('btn btn-secondary btn-sm');
    $('#dataTab_filter input').addClass('border border-none');
});

$('.sel-pen').click(function() {
    editorTab.columns(5).search('En curso').draw();
    $('.sel-text').text('Proyectos en curso');
    $('.dropdown, .dropdown-menu-right').removeClass('show');
});
$('.sel-fin').click(function() {
    editorTab.columns(5).search('Finalizado').draw();
    $('.sel-text').text('Proyectos finalizados');
    $('.dropdown, .dropdown-menu-right').removeClass('show');
});
$('.sel-all').click(function() {
    editorTab.columns(5).search('').draw();
    $('.sel-text').text('Todos los proyectos');
    $('.dropdown, .dropdown-menu-right').removeClass('show');
});

</script>
 @endsection