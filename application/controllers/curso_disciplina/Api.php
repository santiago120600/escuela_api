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

    public function cursoDisciplinas_get()
    {
        if ($this->get('pid')) {
            $result = $this->DAO->selectEntity('curso_disciplina_view', array('curso_id' => $this->get('pid')), true);
        } else {
            $result = $this->DAO->selectEntity('curso_disciplina_view');
        }
        $response = array(
            "status" => 200,
            "status_text" => "success",
            "api" => "curso_disciplina/api/cursoDisciplinas",
            "method" => "GET",
            "message" => "Listado de cursos",
            "data" => $result,
        );
        $this->response($response, 200);
    }

    public function cursoDisciplinas_post()
    {
        $this->form_validation->set_data($this->post());

        $this->form_validation->set_rules('pCurso', 'Clave de curso', 'required|callback_valid_curso');
        $this->form_validation->set_rules('pDisciplina', 'Clave de disciplina', 'required|callback_valid_disciplina');
        if ($this->form_validation->run()) {
            $data = array(
                'curso_fk' => $this->post('pCurso'),
                'disciplina_fk' => $this->post('pDisciplina'),
            );
            $this->DAO->saveOrUpdate('curso_disciplina', $data);
            $response = array(
                "status" => 200,
                "status_text" => "succes",
                "api" => "curso_disciplina/api/cursoDisciplinas",
                "method" => "POST",
                "message" => "Registro correcto",
                "data" => null,
            );

        } else {
            $response = array(
                "status" => 500,
                "status_text" => "error",
                "api" => "curso_disciplina/api/cursoDisciplinas",
                "method" => "POST",
                "message" => "Error al registrar el curso-disciplina",
                "errors" => $this->form_validation->error_array(),
                "data" => null,
            );
        }
        $this->response($response, 200);
    }

    public function valid_disciplina($value)
    {
        if ($value) {
            $disciplina_exists = $this->DAO->selectEntity('disciplina', array('disciplina_id' => $value), true);
            if ($disciplina_exists) {
                return true;
            } else {
                $this->form_validation->set_message('valid_disciplina', 'La clave del campo {field} no es correcto');
                return false;
            }
        } else {
            $this->form_validation->set_message('valid_disciplina', 'El campo {field} es requerido');
            return false;
        }
    }

    public function valid_curso($value)
    {
        if ($value) {
            $curso_exists = $this->DAO->selectEntity('curso', array('curso_id' => $value), true);
            if ($curso_exists) {
                return true;
            } else {
                $this->form_validation->set_message('valid_curso', 'La clave del campo {field} no es correcto');
                return false;
            }
        } else {
            $this->form_validation->set_message('valid_curso', 'El campo {field} es requerido');
            return false;
        }
    }

    
    // http://localhost/api_escuela/index.php/curso_disciplina/api/cursoDisciplinas/idcurso/4/idmateria/5
    public function cursoDisciplinas_put()
    {
        if ($this->get('idcurso') && $this->get('idmateria')) {
            $curso_exists = $this->DAO->selectEntity('curso', array('curso_id' => $this->get('idcurso')), true);
            $materia_exists = $this->DAO->selectEntity('disciplina', array('disciplina_id' => $this->get('idmateria')), true);
            if ($curso_exists) {
                if ($materia_exists) {
                    $this->form_validation->set_data($this->put());
    
                    $this->form_validation->set_rules('pDisciplina', 'Clave de disciplina', 'required|callback_valid_disciplina');
    
                    if ($this->form_validation->run()) {
                        $data = array(
                            'disciplina_fk' => $this->put('pDisciplina')
                        );
                        $this->DAO->saveOrUpdate('curso_disciplina', $data, array('curso_fk' => $this->get('idcurso'),'disciplina_fk'=>$this->get('idmateria')));
    
                        $response = array(
                            "status" => 200,
                            "status_text" => "succes",
                            "api" => "curso_disciplina/api/cursoDisciplinas",
                            "method" => "PUT",
                            "message" => "Curso-disciplina actualizado correctamente",
                            "data" => null,
                        );
                    } else {
                        $response = array(
                            "status" => 500,
                            "status_text" => "error",
                            "api" => "curso_disciplina/api/cursoDisciplinas",
                            "method" => "PUT",
                            "message" => "Error al actualizar el curso-disciplina",
                            "errors" => $this->form_validation->error_array(),
                            "data" => null,
                        );
                    }
                }else{
                    $response = array(
                        "status" => 404,
                        "status_text" => "error",
                        "api" => "curso_disciplina/api/cursoDisciplinas",
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
                    "api" => "curso_disciplina/api/cursoDisciplinas",
                    "method" => "PUT",
                    "message" => "curso no localizado",
                    "errors" => array(),
                    "data" => null,
                );
            }
        } else {
            $response = array(
                "status" => 404,
                "status_text" => "error",
                "api" => "curso_disciplina/api/cursoDisciplinas",
                "method" => "PUT",
                "message" => "Identificador no localizado, La clave de curso o disciplina no fue enviada",
                "errors" => array(),
                "data" => null,
            );
        }
        $this->response($response, 200);
    }

    // http://localhost/api_escuela/index.php/curso_disciplina/api/cursoDisciplinas/idcurso/4/idmateria/3
    public function cursoDisciplinas_delete()
    {
        if ($this->get('idcurso') && $this->get('idmateria')) {
            $curso_exists = $this->DAO->selectEntity('curso', array('curso_id' => $this->get('idcurso')), true);
            $materia_exists = $this->DAO->selectEntity('disciplina', array('disciplina_id' => $this->get('idmateria')), true);

            if ($curso_exists) {
                if ($materia_exists) {
                    $this->DAO->deleteItemEntity('curso_disciplina', array('curso_fk' => $this->get('idcurso'),'disciplina_fk'=>$this->get('idmateria')));
                    $response = array(
                        "status" => 200,
                        "status_text" => "succes",
                        "api" => "curso_disciplina/api/cursoDisciplinas",
                        "method" => "DELETE",
                        "message" => "Curso-disciplina borrado correctamente",
                        "data" => null,
                    );
                }else{
                    $response = array(
                        "status" => 404,
                        "status_text" => "error",
                        "api" => "curso_disciplina/api/cursoDisciplinas",
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
                    "api" => "curso_disciplina/api/cursoDisciplinas",
                    "method" => "DELETE",
                    "message" => "Curso no localizado",
                    "errors" => array(),
                    "data" => null,
                );
            }
        } else {
            $response = array(
                "status" => 404,
                "status_text" => "error",
                "api" => "curso_disciplina/api/cursoDisciplinas",
                "method" => "DELETE",
                "message" => "Identificador no localizado, La clave de curso o disciplina no fue enviada",
                "errors" => array(),
                "data" => null,
            );
        }
        $this->response($response, 200);
    }

}
