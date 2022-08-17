<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Custom;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Proyecto;
use App\Models\Tarea;
use App\Models\cat_docs;
use App\Models\Documento;

class TareaController extends Controller
{
    protected $bg;
    public function __construct()
    {
        $this->bg = Custom::where('id_user', 3)->get()->last();
    }
    public function crud(Request $request)
    {
        $respuesta = [];
        $err = "Hubo un problema. Consulte un administrador.";
        try {
            if ($request->has('index')) {
                if ($request->index == "load") {
                    $tarea = Tarea::where('id_user', Auth::user()->id)->orWhere('responsable', Auth::user()->id)->get(['id AS DT_RowId', 'tareas.*']);
                    foreach ($tarea as $tar) {
                        $tar->responsable = $tar->usuarios->nombreCompleto;
                    }
                    $respuesta['data'] = $tarea;
                    return response()->json($respuesta);
                } elseif ($request->index == "get") {
                    $tarea = Tarea::where('id_user', Auth::user()->id)->where('id_proyecto', $request->id_proyecto)->get(['id AS DT_RowId', 'tareas.*']);
                    $respuesta['data'] = $tarea;
                    return response()->json($respuesta);
                } elseif ($request->index == "save") {
                    if ($request->nombre == '') {
                        $err = "Ingrese el nombre.";
                    } elseif ($request->descripcion == '') {
                        $err = "Ingrese la descripción.";
                    } elseif (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $request->inicio) != 1) {
                        $err = "Fecha inicio inválida.";
                    } elseif (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $request->final) != 1) {
                        $err = "Fecha final inválida.";
                    }
                    $tarea = new Tarea();
                    $tarea->nombre = $request->nombre;
                    $tarea->descripcion = $request->descripcion;
                    $tarea->inicio = $request->inicio;
                    if($request->has('id_proyecto')) {
                    $tarea->id_proyecto = $request->id_proyecto;
                    }
                    $tarea->area = Auth::user()->area;
                    $tarea->responsable = Auth::user()->id;
                    $tarea->final = $request->final;
                    $tarea->id_user = Auth::user()->id;
                    $tarea->estado = "En curso";
                    $tarea->save();
                    $nuevo = Tarea::orderBy('created_at', 'desc')->first();
                    $respuesta['data'][0] = $nuevo;
                    return response()->json($respuesta);
                } elseif ($request->index == "remove") {
                    $tarea = Tarea::where('id', $request->id)->delete();
                    $data = Tarea::all();
                    return response()->json($data);
                } elseif ($request->index == "update") {
                    if ($request->nombre == '') {
                        $err = "Ingrese el nombre.";
                    } elseif ($request->descripcion == '') {
                        $err = "Ingrese la descripción.";
                    } elseif (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $request->inicio) != 1) {
                        $err = "Fecha inicio inválida.";
                    } elseif (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $request->final) != 1) {
                        $err = "Fecha final inválida.";
                    }
                    $tarea = Tarea::where('id', $request->id)->get()->last();
                    $tarea->id = $request->id;
                    $tarea->nombre = $request->nombre;
                    $tarea->descripcion = $request->descripcion;
                    $tarea->inicio = $request->inicio;
                    $tarea->final = $request->final;
                    if($request->has('estado')){
                        if ($tarea->estado != '') {
                            $tarea->estado = $request->estado;
                        }
                    }else{
                        $tarea->estado = 'En curso';
                    }
                    $tarea->update();
                    $respuesta['data'][0] = $tarea;
                    return response()->json($respuesta);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $err, 'code' => $e], 404);
        }
    }
    //
    public function index()
    {
        $bg = $this->bg;
        $tareas = Tarea::where('id_user', Auth::user()->id)->get();
        return view('tareas.index', compact('bg', 'tareas'));
    }

    public function create(Proyecto $proyecto)
    {
        $bg = $this->bg;
        return view('tareas.create', compact('bg', 'proyecto'));
    }
    public function create2(Tarea $tarea)
    {
        $bg = $this->bg;
        $usuarios = User::get()->where('area',$tarea->area)->pluck('nombreCompleto', 'id'); 
        return view('tareas.responsable', compact('bg', 'tarea','usuarios'));
    }

    public function documentos(Tarea $tarea){
        $bg = $this->bg;
        $cat_doc = cat_docs::where('iactivo',1)->pluck('nombre', 'id'); 
        $documentos = Documento::where('id_user', Auth::user()->id)->where('id_tarea', $tarea->id)->orderBy('created_at', 'DESC')->get();
        foreach ($documentos as $documento) {
            $xx = strtolower($documento->extension);
            if($xx=='xls' || $xx=='csv' || $xx=='xlsx'){
                $documento->tipo = 'fa-file-excel-o text-success';
            }elseif($xx=='pdf') {
                $documento->tipo = 'fa-file-pdf-o text-danger';
            }elseif($xx=='doc' || $xx=='docx') {
                $documento->tipo = 'fa-file-word-o text-info';
            }elseif($xx=='ppt' || $xx=='pptx') {
                $documento->tipo = 'fa-file-powerpoint-o text-secondary';
            }elseif($xx=='zip' || $xx=='rar' || $xx=='bin') {
                $documento->tipo = 'fa-file-zip-o text-primary';
            }elseif($xx=='txt') {
                $documento->tipo = 'fa-file-text-o text-warning';
            }elseif($xx=='png' || $xx == 'jpg' || $xx == 'jpeg' || $xx == 'gif' || $xx == 'raw' || $xx == 'svg' || $xx == 'psd') {
                $documento->tipo = 'fa-file-image-o text-dribbble';
            }elseif($xx=='js' || $xx == 'php' || $xx == 'html' || $xx == 'css' || $xx == 'java' || $xx == 'sql') {
                $documento->tipo = 'fa-file-code-o text-instagram';
            }elseif($xx=='mp3' || $xx == 'mp4' || $xx == 'mov' || $xx == 'avi' || $xx == 'wav') {
                $documento->tipo = 'fa-file-audio-o text-light';
            }else{
                $documento->tipo = 'fa-file-o text-dark';
            }
        }
        return view('tareas.documentos',compact('bg','documentos','tarea','cat_doc'));     
     }

     public function store(Request $request, Tarea $tarea){
        try{
        $archi=new Documento();
        $archi->nombre=$request->nombre;
        $nombreout = str_replace(' ', '-', $request->nombre);
        $archi->descripcion=$request->descripcion;
        $archi->url='/SGPI/documents/tareas';
        $archi->url_edit='documents/tareas';
        $archi->id_user=Auth::user()->id;
        $usuario = User::where('id', Auth::user()->id)->get()->last();
        $archi->id_area=$usuario->area;
        $archi->cat_doc=$request->tipo;
        $mytime = Carbon::now()->format('d-m-Y_H-i');
        $extension = $request->archivo->getClientOriginalExtension();
        $nombre = Auth::user()->id.'_'.$nombreout.'_'.$mytime.'.'.$extension;
        $archi->nombre_doc=$nombre;
        $archi->tipo='Tarea';
        $archi->extension=$extension;
        $archi->id_tarea=$tarea->id;
        $archi->save();
        $request->archivo->move('documents/tareas', $nombre);

            return back()->with('ok', 'ok');
        }catch(\Exception $e){
            return back()->with('nook', $e->getMessage());
        }
    }

    public function store2(Request $request, Tarea $tarea){
        try{
        $tarea->responsable = $request->responsable;
        $tarea->update();

            return back()->with('ok', 'ok');
        }catch(\Exception $e){
            return back()->with('nook', $e->getMessage());
        }
    }

    /*public function save(Request $request)
    {
        $respuesta = [];
        if ($request->has('index')) {
            $tar = Tarea::where('id_user', Auth::user()->id)->where('id_proyecto', $request->id_proyecto)->get(['id AS DT_RowId', 'tareas.*']);
            $respuesta['data'] = $tar;
            return response()->json($respuesta);
        } else {
            $tar = new Tarea();
            $tar->nombre = $request->nombre;
            $tar->descripcion = $request->descripcion;
            $tar->inicio = $request->inicio;
            $tar->final = $request->final;
            $tar->responsable = Auth::user()->id;
            $tar->id_proyecto = $request->id_proyecto;
            $tar->id_area = Auth::user()->area;
            $tar->id_user = Auth::user()->id;
            $tar->estado = "En curso";
            $tar->save();
            $nuevo = Tarea::orderBy('created_at', 'desc')->where('id_user', Auth::user()->id)->where('id_proyecto', $request->id_proyecto)->first();
            $respuesta['data'][0] = $nuevo;
            return response()->json($respuesta);
        }
    }

    public function remove(Request $request)
    {
        $tar = Tarea::where('id', $request->id)->delete();
        $data = Tarea::all();
        return response()->json($data);
    }

    public function update(Request $request)
    {
        $respuesta = [];
        $tar = Tarea::where('id', $request->id)->get()->last();
        $tar->id = $request->id;
        $tar->nombre = $request->nombre;
        $tar->descripcion = $request->descripcion;
        $tar->inicio = $request->inicio;
        $tar->final = $request->final;
        $tar->update();
        $respuesta['data'][0] = $tar;
        return response()->json($respuesta);
    }*/
}
