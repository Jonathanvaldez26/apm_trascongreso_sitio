<?php
namespace App\controllers;
defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\Controller;
use \App\models\Home AS HomeDao;
use App\models\RegistroAcceso as RegistroAccesoDao;
use \App\models\Talleres as TalleresDao;

class Home extends Controller{

    private $_contenedor;

    function __construct(){
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header',$this->_contenedor->header());
        View::set('footer',$this->_contenedor->footer());
    }

    public function getUsuario(){
      return $this->__usuario;
    }

    public function index() {
     $extraHeader =<<<html
      <link id="pagestyle" href="/assets/css/style.css" rel="stylesheet" />
      <title>
            Home
      </title>
html;

        


        $data_user = HomeDao::getDataUser($this->__usuario);

        $productos_pendientes_comprados = HomeDao::getProductosPendComprados($data_user['user_id']);
        $checks = '';


        foreach($productos_pendientes_comprados as $key => $value) {
            $disabled = '';
            $checked = '';
            $pend_validar ='';

            if($value['estatus_compra'] == 1){
                $disabled = 'disabled';
                $checked = 'checked';
                $pend_validar ='Pagado y validado por APM';
            }else if($value['estatus_compra'] == null){
                $pend_validar = 'Pagado pero esta pendiente de validar';
            }

            if($value['max_compra'] <= 1){
                $numero_productos = '<input type="number" id="numero_articulos'.$value['id_producto'].'" name="numero_articulos" value="'.$value['max_compra'].'" style="border:none;" readonly>';
            }else{
                $numero_productos = '<select class="form-control select_numero_articulos" id="numero_articulos'.$value['id_producto'].'" name="numero_articulos" data-id-producto="'.$value['id_producto'].'" data-precio="'.$value['precio_publico'].'">';
                for($i = 1; $i <= $value['max_compra']; $i++){                    
                    $numero_productos .= '<option value="'.$i.'">'.$i.'</option>';                
                }
                $numero_productos .= '</select>';
            }

            $checks .= <<<html
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-check">
                            <input class="form-check-input checks_product" type="checkbox" value="{$value['id_producto']}" id="check_curso_{$value['id_producto']}" name="checks_cursos[]" {$disabled} {$checked} data-precio="{$value['precio_publico']}">
                            <label class="form-check-label" for="check_curso_{$value['id_producto']}">
                                {$value['nombre_producto']} <span style="font-size: 13px; text-decoration: underline; color: green;">{$pend_validar}</span>
                            </label>
                        </div>
                    </div>
                   
                    <div class="col-md-2">
                        {$value['precio_publico']} - {$value['tipo_moneda']}
                    </div>

                    <div class="col-md-2">
                        {$numero_productos}
                    </div>
                </div>

                <hr>
                  
html;            
                $numero_productos = '';

        }

        $productos_no_comprados = HomeDao::getProductosNoComprados($data_user['user_id']);

        foreach($productos_no_comprados as $key => $value) {

            if($value['max_compra'] <= 1){
                $numero_productos = '<input type="number" id="numero_articulos'.$value['id_producto'].'" name="numero_articulos" value="'.$value['max_compra'].'" style="border:none;" readonly>';
            }else{
                $numero_productos = '<select class="form-control select_numero_articulos" id="numero_articulos'.$value['id_producto'].'" name="numero_articulos" data-id-producto="'.$value['id_producto'].'"  data-precio="'.$value['precio_publico'].'">';
                for($i = 1; $i <= $value['max_compra']; $i++){                    
                    $numero_productos .= '<option value="'.$i.'">'.$i.'</option>';                
                }
                $numero_productos .= '</select>';
            }
            
            $checks .= <<<html

            <div class="row">
                <div class="col-md-8">
                    <div class="form-check">
                        <input class="form-check-input checks_product" type="checkbox" value="{$value['id_producto']}" id="check_curso_{$value['id_producto']}" name="checks_cursos[]" data-precio="{$value['precio_publico']}">
                        <label class="form-check-label" for="check_curso_{$value['id_producto']}">
                            {$value['nombre_producto']}
                        </label>
                    </div>
                </div>
               
                <div class="col-md-2">
                    {$value['precio_publico']} - {$value['tipo_moneda']}
                </div>

                <div class="col-md-2">
                       {$numero_productos}
                </div>

            </div>

            <hr>
            
               
html;            
            $numero_productos = '';

        }


  
        View::set('header',$this->_contenedor->header($extraHeader));   
        View::set('datos',$data_user);    
        View::set('checks',$checks);    
        View::render("principal_all");
    }

    public function generateModalComprar($datos){
        $modal = <<<html
        <div class="modal fade" id="comprar-curso{$datos['id_curso']}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                Comprar curso
                </h5>

                <span type="button" class="btn bg-gradient-danger" data-dismiss="modal" aria-label="Close">
                    X
                </span>
            </div>
            <div class="modal-body">
              ...
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary">Save changes</button>
            </div>
          </div>
        </div>
      </div>
html;



        return $modal;
    }

    public function getData(){
      echo $_POST['datos'];
    }

}
