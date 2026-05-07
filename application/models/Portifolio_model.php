<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Portifolio_model extends ModelAuth {
	protected $tabela = 'portifolio';
	protected $campo_id = 'id_portifolio';
	protected $campo_principal = 'nome';

	public function retornar_todos(){
		$sql = 'SELECT * FROM portifolio';
		return $this->db->query($sql);
	}

	public function get_slugs(){
		$sql = 'SELECT * FROM consulta ORDER BY slug ASC';
		return $this->db->query($sql);
	}
}
