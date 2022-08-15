<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CatAccesosController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\contraresController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\DireccionController;
use App\Http\Controllers\SubdireccionController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\TareaController;
use App\Http\Controllers\SubtareaController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\NotaController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\CatDocsController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/contrares', [contraresController::class,'index'])->name('contrares');
Route::post('contrares', [contraresController::class,'actua'])->name('actua');

Auth::routes();
Route::group(['namespace'=>'admin', 'middleware' => 'val_acceso'],function(){

    Route::post('detalles/subdireccion', [SubdireccionController::class,'details'])->name('subdirecciones.details');
    Route::post('detalles/departamento', [DepartamentoController::class,'details'])->name('departamentos.details');
    Route::post('detalles/proyecto', [ProyectoController::class,'details'])->name('proyectos.details');

    Route::get('notas',[NotaController::class,'index'])->name('notas.index');
    Route::post('crud/notas', [NotaController::class,'crud'])->name('notas.crud');

    Route::get('tareas',[TareaController::class,'index'])->name('tareas.index');
    Route::get('tareas/create/{proyecto}',[TareaController::class,'create'])->name('tareas.create');
    Route::post('crud/tarea', [TareaController::class,'crud'])->name('tareas.crud');
    Route::get('tareas/documentos/{tarea}', [TareaController::class,'documentos'])->name('tareas.documentos');
    Route::post('tareas/save/{tarea}', [TareaController::class,'store'])->name('tareas.store');


    Route::get('subtareas',[SubtareaController::class,'index'])->name('subtareas.index');
    Route::get('subtareas/create/{tarea}',[SubtareaController::class,'create'])->name('subtareas.create');
    Route::post('crud/subtarea', [SubtareaController::class,'crud'])->name('subtareas.crud');
    Route::get('subtareas/documentos/{subtarea}', [SubtareaController::class,'documentos'])->name('subtareas.documentos');
    Route::post('subtareas/save/{subtarea}', [SubtareaController::class,'store'])->name('subtareas.store');

    Route::get('proyectos',[ProyectoController::class,'index'])->name('proyectos.index');
    Route::post('crud/proyecto', [ProyectoController::class,'crud'])->name('proyectos.crud');
    Route::get('proyectos/grafica/{proyecto}', [ProyectoController::class,'graphic'])->name('proyectos.grafica');
    Route::get('proyectos/documentos/{proyecto}', [ProyectoController::class,'documentos'])->name('proyectos.documentos');
    Route::post('proyectos/save/{proyecto}', [ProyectoController::class,'store'])->name('proyectos.store');

    Route::get('home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::get('accesos',[CatAccesosController::class,'index'])->name('accesos.index');
    Route::get('accesos/create',[CatAccesosController::class,'create'])->name('accesos.create');
    Route::post('acceso/save/acceso', [CatAccesosController::class,'store'])->name('accesos.store');
    Route::get('accesos/show/{acceso}', [CatAccesosController::class,'show'])->name('accesos.show');
    Route::put('accesos/update/{acceso}', [CatAccesosController::class,'update'])->name('accesos.update');
    Route::get('accesos/destroy/{acceso}', [CatAccesosController::class,'destroy'])->name('accesos.destroy');
    Route::get('accesos/inactivar/{acceso}', [CatAccesosController::class,'inactivar'])->name('accesos.inactivar');
    Route::get('accesos/activar/{acceso}', [CatAccesosController::class,'activar'])->name('accesos.activar');

    Route::get('direcciones',[DireccionController::class,'index'])->name('direcciones.index');
    Route::get('direcciones/create',[DireccionController::class,'create'])->name('direcciones.create');
    Route::get('direcciones/update/{direccion}',[DireccionController::class,'create'])->name('direcciones.update');
    Route::post('direcciones/save/{direccion?}', [DireccionController::class,'store'])->name('direcciones.store');
    Route::get('direcciones/activar/{direccion}', [DireccionController::class,'activar'])->name('direcciones.activar');
    Route::get('direcciones/inactivar/{direccion}', [DireccionController::class,'inactivar'])->name('direcciones.inactivar');

    Route::get('subdirecciones',[SubdireccionController::class,'index'])->name('subdirecciones.index');
    Route::get('subdirecciones/create',[SubdireccionController::class,'create'])->name('subdirecciones.create');
    Route::get('subdirecciones/upadte/{subdireccion}',[SubdireccionController::class,'create'])->name('subdirecciones.update');
    Route::post('subdirecciones/save/{subdireccion?}', [SubdireccionController::class,'store'])->name('subdirecciones.store');
    Route::get('subdirecciones/activar/{subdireccion}', [SubdireccionController::class,'activar'])->name('subdirecciones.activar');
    Route::get('subdirecciones/inactivar/{subdireccion}', [SubdireccionController::class,'inactivar'])->name('subdirecciones.inactivar');

    Route::get('departamentos',[DepartamentoController::class,'index'])->name('departamentos.index');
    Route::get('departamentos/create',[DepartamentoController::class,'create'])->name('departamentos.create');
    Route::get('departamentos/update/{departamento}',[DepartamentoController::class,'create'])->name('departamentos.update');
    Route::post('departamentos/save/{departamento?}', [DepartamentoController::class,'store'])->name('departamentos.store');
    Route::get('departamentos/activar/{departamento}', [DepartamentoController::class,'activar'])->name('departamentos.activar');
    Route::get('departamentos/inactivar/{departamento}', [DepartamentoController::class,'inactivar'])->name('departamentos.inactivar');

    Route::get('archivos',[DocumentoController::class,'index'])->name('archivos.index');
    Route::get('archivos/create',[DocumentoController::class,'create'])->name('archivos.create');
    Route::get('archivos/update/{documento}',[DocumentoController::class,'create'])->name('archivos.update');
    Route::post('archivos/save/{documento?}', [DocumentoController::class,'store'])->name('archivos.store');
    Route::get('archivos/destroy/{documento}', [DocumentoController::class,'destroy'])->name('archivos.destroy');

    Route::get('documentos',[CatDocsController::class,'index'])->name('documentos.index');
    Route::get('documentos/create',[CatDocsController::class,'create'])->name('documentos.create');
    Route::get('documentos/update/{documento}',[CatDocsController::class,'create'])->name('documentos.update');
    Route::post('documentos/save/{documento?}', [CatDocsController::class,'store'])->name('documentos.store');
    Route::get('documentos/activar/{documento}', [CatDocsController::class,'activar'])->name('documentos.activar');
    Route::get('documentos/inactivar/{documento}', [CatDocsController::class,'inactivar'])->name('documentos.inactivar');

    Route::get('admins',[AdminController::class,'index'])->name('admins.index');
    Route::get('admins/create/admin',[AdminController::class,'create'])->name('admins.create');
    Route::get('admins/activar/{admin}', [AdminController::class,'activar'])->name('admins.activar');
    Route::get('admins/activartec/{admin}', [AdminController::class,'activartec'])->name('admins.activartec');
    Route::get('admins/inactivar/{admin}', [AdminController::class,'inactivar'])->name('admins.inactivar');
    Route::get('admins/asistio/{admin}', [AdminController::class,'asistio'])->name('admins.asistio');
    Route::get('admins/noasistio/{admin}', [AdminController::class,'noasistio'])->name('admins.noasistio');
    Route::get('admins/disponible/{admin}', [AdminController::class,'disponible'])->name('admins.disponible');
    Route::get('admins/nodisponible/{admin}', [AdminController::class,'nodisponible'])->name('admins.nodisponible');
    Route::post('admins/save/admin', [AdminController::class,'store'])->name('admins.store');
    Route::get('admins/asignar/{admin}', [AdminController::class,'asignar'])->name('admins.asignar');
    Route::post('admins/usuario/{admin}', [AdminController::class,'cuenta'])->name('admins.cuenta');

    Route::get('usuarios',[UserController::class,'index'])->name('usuarios.index');
    Route::get('usuarios/perfil',[UserController::class,'perfil'])->name('usuarios.perfil');
    Route::get('usuarios/inactivar/{usuario}', [UserController::class,'inactivar'])->name('usuarios.inactivar');
    Route::get('usuarios/activar/{usuario}', [UserController::class,'activar'])->name('usuarios.activar');
    Route::post('usuarios/custom', [UserController::class,'custom'])->name('usuarios.custom');
    Route::post('usuarios/save/{usuario}', [UserController::class,'store'])->name('usuarios.store');
    Route::get('usuarios/show/{usuario}', [UserController::class,'show'])->name('usuarios.show');
    Route::get('usuarios/create/usuario', [UserController::class,'create'])->name('usuarios.create');
    Route::get('usuarios/area/usuario', [UserController::class,'area'])->name('usuarios.area');
    Route::post('usuarios/update/{usuario}', [UserController::class,'update'])->name('usuarios.update');
    Route::post('usuarios/update2/{usuario}', [UserController::class,'update_area'])->name('usuarios.update2');

    Route::get('pdf/orden/{orden}', [PDFController::class,'generatePDF'])->name('pdf.show');

});