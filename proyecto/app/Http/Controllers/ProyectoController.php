<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Custom;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Proyecto;
use App\Models\User;
use App\Models\Tarea;
use App\Models\Subtarea;
use App\Models\cat_docs;
use App\Models\Documento;

class ProyectoController extends Controller
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
                    $proyecto = Proyecto::where('id_user', Auth::user()->id)->get(['id AS DT_RowId', 'proyectos.*']);
                    $respuesta['data'] = $proyecto;
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
                    $proyecto = new Proyecto();
                    $proyecto->nombre = $request->nombre;
                    $proyecto->descripcion = $request->descripcion;
                    $proyecto->inicio = $request->inicio;
                    $proyecto->final = $request->final;
                    $proyecto->id_user = Auth::user()->id;
                    $usuario = User::where('id', Auth::user()->id)->get()->last();
                    $proyecto->area = $usuario->area;
                    $proyecto->estado = "En curso";
                    $proyecto->save();
                    $nuevo = Proyecto::orderBy('created_at', 'desc')->first();
                    $respuesta['data'][0] = $nuevo;
                    return response()->json($respuesta);
                } elseif ($request->index == "remove") {
                    $proyecto = Proyecto::where('id', $request->id)->delete();
                    $data = Proyecto::all();
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
                    $proyecto = Proyecto::where('id', $request->id)->get()->last();
                    $proyecto->id = $request->id;
                    $proyecto->nombre = $request->nombre;
                    $proyecto->descripcion = $request->descripcion;
                    $proyecto->inicio = $request->inicio;
                    $proyecto->final = $request->final;
                    if ($proyecto->estado != '') {
                        $proyecto->estado = $request->estado;
                    }
                    $proyecto->update();
                    $respuesta['data'][0] = $proyecto;
                    return response()->json($respuesta);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $err, 'code' => $e], 404);
        }
    }

    public function graphic(Proyecto $proyecto){
        $bg = $this->bg;
        $tareas_id = [];
        $tar = [];
        $sub = [];
        $pro = [];
        $tareas = Tarea::where('id_proyecto', $proyecto->id)->where('id_user', Auth::user()->id)->orderBy('created_at', 'ASC')->get();
        foreach ($tareas as $key => $tarea) {
            $tareas_id[$key] = $tarea->id;
        }
        $subtareas = Subtarea::WhereIn('id_tarea', $tareas_id)->get();
        foreach ($tareas as $key => $tarea) {
            foreach ($subtareas as $key => $subtarea) {
                if($subtarea->id_tarea == $tarea->id){
                    $sub[] = [
                        'url' => $subtarea->id,
                        'client' => $subtarea->nombre,
                        'type' => $subtarea->descripcion,
                        'name' => $subtarea->tareas->nombre,
                        'fromDate' => $subtarea->inicio,
                        'toDate' => $subtarea->final,
                        'color' => 'subtarea',
                        ];
                }else{
                    $tar[] = [
                        'url' => $tarea->id,
                        'client' => $tarea->nombre,
                        'type' => $tarea->descripcion,
                        'name' => $tarea->nombre,
                        'fromDate' => $tarea->inicio,
                        'toDate' => $tarea->final,
                        'color' => 'tarea',
                        ];
                }
                
            }
        }
        $pro = array_merge($tar,$sub);
        return view('proyectos.graphic',compact('bg','pro','proyecto'));     
     }

     public function documentos(Proyecto $proyecto){
        $bg = $this->bg;
        $cat_doc = cat_docs::where('iactivo',1)->pluck('nombre', 'id'); 
        $documentos = Documento::where('id_user', Auth::user()->id)->where('id_proyecto', $proyecto->id)->orderBy('created_at', 'DESC')->get();
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
        return view('proyectos.documentos',compact('bg','documentos','proyecto','cat_doc'));     
     }

     public function store(Request $request,Proyecto $proyecto){
        try{
        $archi=new Documento();
        $archi->nombre=$request->nombre;
        $nombreout = str_replace(' ', '-', $request->nombre);
        $archi->descripcion=$request->descripcion;
        $archi->url='/SGPI/documents/proyectos';
        $archi->url_edit='documents/proyectos';
        $archi->id_user=Auth::user()->id;
        $usuario = User::where('id', Auth::user()->id)->get()->last();
        $archi->id_area=$usuario->area;
        $archi->cat_doc=$request->tipo;
        $mytime = Carbon::now()->format('d-m-Y_H-i');
        $extension = $request->archivo->getClientOriginalExtension();
        $nombre = Auth::user()->id.'_'.$nombreout.'_'.$mytime.'.'.$extension;
        $archi->nombre_doc=$nombre;
        $archi->tipo='Proyecto';
        $archi->extension=$extension;
        $archi->id_proyecto=$proyecto->id;
        $archi->save();
        $request->archivo->move('documents/proyectos', $nombre);

            return back()->with('ok', 'ok');
        }catch(\Exception $e){
            return back()->with('nook', $e->getMessage());
        }
    }

     public function details(Request $request){
        if($request->apd == 'proyecto'){
            $proyecto = Proyecto::where('id', $request->ide)->get()->last();
            if($proyecto == 'undefined' || $proyecto == null){
            $proyecto = new Proyecto();
            $proyecto->nombre = 'Tarea independiente';
            $proyecto->descripcion = 'Sin proyecto raíz';
        }else{
            $proyecto->descripcion = 'Proyecto raíz';
        }
        return response()->json($proyecto);
        }elseif ($request->apd == 'tarea') {
            $tarea = Tarea::where('id', $request->ide)->get()->last();
            $proyecto = Proyecto::where('id', $tarea->id_proyecto)->get()->last();
            if($proyecto == 'undefined' || $proyecto == null){
            $proyecto = new Proyecto();
            $proyecto->nombre = $tarea->nombre;
            $proyecto->descripcion = 'Tarea raíz';
        }else{
            $proyecto->descripcion = 'Proyecto raíz';
        }
        return response()->json($proyecto);
        }
        
    }
}
