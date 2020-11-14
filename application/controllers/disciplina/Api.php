<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Api extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('DAO');
    }

    // curl -X GET http://localhost/api_escuela/index.php/disciplina/Api/disciplinas?pid=2 | jq
    public function disciplinas_get()
    {
        if ($this->get('pid')) {
            $result = $this->DAO->selectEntity('disciplina', array('disciplina_id' => $this->get('pid')), true);
        } else {
            $result = $this->DAO->selectEntity('disciplina');
        }
        $response = array(
            "status" => 200,
            "status_text" => "success",
            "api" => "disciplina/api/disciplinas",
            "method" => "GET",
            "message" => "Listado de disciplinas",
            "data" => $result,
        );
        $this->response($response, 200);
    }

    public function disciplinas_post()
    {
        $this->form_validation->set_data($this->post());

        $this->form_validation->set_rules('pNombre', 'Nombre', 'required|max_length[45]');
        if ($this->form_validation->run()) {
            $data = array(
                'nombre_disciplina' => $this->post('pNombre')
            );
            $this->DAO->saveOrUpdate('disciplina', $data);
            $response = array(
                "status" => 200,
                "status_text" => "succes",
                "api" => "disciplina/api/disciplinas",
                "method" => "POST",
                "message" => "Registro correcto",
                "data" => null,
            );

        } else {
            $response = array(
                "status" => 500,
                "status_text" => "error",
                "api" => "disciplina/api/disciplinas",
                "method" => "POST",
                "message" => "Error al registrar disciplina",
                "errors" => $this->form_validation->error_array(),
                "data" => null,
            );
        }
        $this->response($response, 200);
    }

    public function disciplinas_put()
    {
        if ($this->get('pid')) {
            $curso_exists = $this->DAO->selectEntity('disciplina', array('disciplina_id' => $this->get('pid')), true);
            if ($curso_exists) {
                $this->form_validation->set_data($this->put());

                $this->form_validation->set_rules('pNombre', 'Nombre', 'required|max_length[45]');

                if ($this->form_validation->run()) {
                    $data = array(
                        'nombre_disciplina' => $this->post('pNombre')
                    );
                    $this->DAO->saveOrUpdate('disciplina', $data, array('disciplina_id' => $this->post('pid')));

                    $response = array(
                        "status" => 200,
                        "status_text" => "succes",
                        "api" => "disciplina/api/disciplinas",
                        "method" => "PUT",
                        "message" => "Disciplina actualizada correctamente",
                        "data" => null,
                    );
                } else {
                    $response = array(
                        "status" => 500,
                        "status_text" => "error",
                        "api" => "disciplina/api/disciplinas",
                        "method" => "PUT",
                        "message" => "Error al actualizar disciplina",
                        "errors" => $this->form_validation->error_array(),
                        "data" => null,
                    );
                }

            } else {
                $response = array(
                    "status" => 404,
                    "status_text" => "error",
                    "api" => "disciplina/api/disciplinas",
                    "method" => "PUT",
                    "message" => "Disciplina no localizado",
                    "errors" => array(),
                    "data" => null,
                );
            }
        } else {
            $response = array(
                "status" => 404,
                "status_text" => "error",
                "api" => "disciplina/api/disciplinas",
                "method" => "PUT",
                "message" => "Identificador no localizado, La clave de disciplina no fue enviada",
                "errors" => array(),
                "data" => null,
            );
        }
        $this->response($response, 200);
    }

    public function disciplinas_delete()
    {
        if ($this->get('pid')) {
            $curso_exists = $this->DAO->selectEntity('disciplina', array('disciplina_id' => $this->get('pid')), true);
            if ($curso_exists) {
                $this->DAO->deleteItemEntity('disciplina', array('disciplina_id' => $this->get('pid')));
                $response = array(
                    "status" => 200,
                    "status_text" => "succes",
                    "api" => "disciplina/api/disciplinas",
                    "method" => "DELETE",
                    "message" => "Disciplina borrado correctamente",
                    "data" => null,
                );
            } else {
                $response = array(
                    "status" => 404,
                    "status_text" => "error",
                    "api" => "disciplina/api/disciplinas",
                    "method" => "DELETE",
                    "message" => "Disciplina no localizada",
                    "errors" => array(),
                    "data" => null,
                );
            }
        } else {
            $response = array(
                "status" => 404,
                "status_text" => "error",
                "api" => "disciplina/api/disciplinas",
                "method" => "DELETE",
                "message" => "Identificador no localizado, La clave de disciplina no fue enviada",
                "errors" => array(),
                "data" => null,
            );
        }
        $this->response($response, 200);
    }

}
