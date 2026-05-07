<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Cliente_model extends ModelAuth {
    protected $tabela = 'cliente';
    protected $campo_id = 'id_cliente';
    protected $campo_principal = 'nome_ou_fantasia';

    public function ultimas_aberturas($qtd=150){
        $sql  = 'SELECT tbmain.*, tb2.nome_ou_fantasia AS franquia, tb3.nome_ou_fantasia AS consultor_nome FROM cliente AS tbmain ';
        $sql .= 'LEFT JOIN franquia AS tb2 ON tbmain.id_franquia_fk = tb2.id_franquia ';
        $sql .= 'LEFT JOIN cliente AS tb3 ON tbmain.id_consultor_fk = tb3.id_cliente ';
        $sql .= 'ORDER BY id_cliente DESC LIMIT '.$qtd;
        return $this->db->query($sql);
    }

    public function alterar_usuarios($id_cliente,$dados){
        if($dados!=NULL&&$id_cliente!=null) {
            $this->db->update('usuario', $dados, array('id_cliente_fk'=>$id_cliente));
            if($this->db->affected_rows()>0){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function retornar_consultores(){
        $this->db->where(array('consultor'=>'1'));
        return $this->db->get('cliente');
    }


    public function remover_cliente_consultas($id_cliente=null){
        if($id_cliente!=null) {
            $sql = 'DELETE FROM cliente_consulta WHERE id_cliente_fk = '.$id_cliente;
            $this->db->query($sql);
            if ($this->db->affected_rows() > 0) {
                return true;
            } else {
                return false;
            }
        }else{
            return false;
        }
    }

    public function retornar_consultores_array(){
        $consultores = $this->retornar_consultores()->result();
        $retorno = array();
        foreach ($consultores as $index => $consultor):
            $retorno[$consultor->id_cliente] = $consultor->nome_ou_fantasia;
        endforeach;
        return $retorno;
    }

    public function retornar_consultas_efetuadas($id_cliente=null, $limite = 100){
        if($id_cliente!=null){
            $this->db->where(array('id_cliente_fk'=>$id_cliente));
            $this->db->order_by('criado_em','DESC');
            $this->db->limit($limite);
            return $this->db->get('consulta_efetuada');
        }else{
            return false;
        }
    }

    public function retornar_consultas_veiculares($id_cliente=null, $limite = 100){
        if($id_cliente!=null){
            $this->db->where(array('id_cliente_fk'=>$id_cliente));
            $this->db->order_by('criado_em','DESC');
            $this->db->limit($limite);
            return $this->db->get('consulta_veicular');
        }else{
            return false;
        }
    }

    public function retornar_cartas($id_cliente=null, $limite = 100){
        if($id_cliente!=null){
            $this->db->where(array('id_cliente_fk'=>$id_cliente));
            $this->db->order_by('criado_em','DESC');
            $this->db->limit($limite);
            return $this->db->get('carta');
        }else{
            return false;
        }
    }

    public function retornar_negativacoes($id_cliente=null, $limite = 100){
        if($id_cliente!=null){
            $this->db->where(array('id_cliente_fk'=>$id_cliente));
            $this->db->order_by('criado_em','DESC');
            $this->db->limit($limite);
            return $this->db->get('negativacao');
        }else{
            return false;
        }
    }

    public function retornar_baixas($id_cliente=null, $limite = 100){
        if($id_cliente!=null){
            $this->db->where(array('id_cliente_fk'=>$id_cliente));
            $this->db->order_by('criado_em','DESC');
            $this->db->limit($limite);
            return $this->db->get('negativacao_baixa');
        }else{
            return false;
        }
    }

    public function retornar_usuarios($id_cliente=null){
        if($id_cliente!=null){
            $this->db->where(array('id_cliente_fk'=>$id_cliente));
            return $this->db->get('usuario');
        }else{
            return false;
        }
    }

    public function retornar_consulta_efetuada($id_consulta_efetuada=null){
        if($id_consulta_efetuada!=null){
            $this->db->where(array('id_consulta_efetuada'=>$id_consulta_efetuada));
            return $this->db->get('consulta_efetuada');
        }else{
            return false;
        }
    }

    public function retornar_boletos($id_cliente){
        $this->db->where(array('id_cliente_fk'=>$id_cliente));
        return $this->db->get('boleto');
    }

    public function retornar_faturas($id_cliente){
        $this->db->where(array('id_cliente_fk'=>$id_cliente));
        return $this->db->get('fatura');
    }

    public function adicionar_consulta($dados){
        $this->db->insert('cliente_consulta', $dados);
    }

    public function criar_usuario($cliente=null,$nome=null){
        $usuario = 500700+$cliente;
        $senha = $this->gerador_de_senha();

        $dados = array();
        $dados['id_cliente_fk'] = $cliente;
        $dados['status'] = 1;
        $dados['usuario'] = $usuario;
        $dados['nome'] = $nome;
        $dados['senha'] = md5($senha);

        $this->db->insert('usuario',$dados);
        if ($this->db->affected_rows() > 0) {
            return array('usuario'=>$usuario,'senha'=>$senha);
        } else {
            return null;
        }
    }

    function gerador_de_senha() {
        $alphabet = '1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    function retornar_todos(){
        $sql = 'SELECT * FROM cliente ORDER BY nome_ou_fantasia ASC';
        return $this->db->query($sql);
    }

    function retornar_matriz_ativos_ordenado(){
        $sql = 'SELECT * FROM cliente WHERE consultor = 0 AND id_consultor_fk = 3 AND id_franquia_fk = 0 AND status = 1 ORDER BY status DESC, criado_em ASC';
        return $this->db->query($sql);
    }

    function retornar_franquias_ativos_ordenado(){
        $sql = 'SELECT * FROM cliente WHERE consultor = 0 AND id_franquia_fk <> 0 AND status = 1 ORDER BY status DESC, criado_em ASC';
        return $this->db->query($sql);
    }

    function retornar_representantes_ativos_ordenado(){
        $sql = 'SELECT * FROM cliente WHERE consultor = 0 AND id_consultor_fk <> 3 AND status = 1 ORDER BY status DESC, criado_em ASC';
        return $this->db->query($sql);
    }

    function retornar_todos_ordenado_gerenciar(){
        $sql = 'SELECT * FROM cliente WHERE consultor = 0 AND id_franquia_fk = 0 ORDER BY status DESC, criado_em ASC';
        return $this->db->query($sql);
    }

    function retornar_todos_ordenado_gerenciar_franquia($id_franquia){
        $sql = 'SELECT * FROM cliente WHERE consultor = 0 AND id_franquia_fk = '.$id_franquia.' ORDER BY status DESC, criado_em ASC';
        return $this->db->query($sql);
    }

    function retornar_todos_ordenado($order = 'dia_vencimento'){
        $this->db->order_by($order,'ASC');
        return $this->db->get($this->tabela);
    }

    function retornar_agrupado_por_cidade(){
        $sql = 'SELECT UPPER(cidade) AS cidade,COUNT(*) AS qtd,SUM(mensalidade) AS valor FROM `cliente` GROUP BY cidade ORDER BY qtd DESC, valor DESC';
        return $this->db->query($sql);
    }

    function retornar_de_cidade($cidade){
        $sql = 'SELECT * FROM cliente WHERE cidade LIKE "%'.$cidade.'%" ';
        return $this->db->query($sql);
    }

    function retornar_ativos(){
        $sql = 'SELECT nome_ou_fantasia, LOWER(email) AS email FROM cliente WHERE status = 1 AND consultor = 0 GROUP BY email';
        return $this->db->query($sql);
    }

    public function retornar_consulta_mais_barata($slug_consulta=null,$id_cliente=null){
        if($slug_consulta!=null){
            $query  = 'SELECT ';
            $query .= 'tbmain.*,tb2.chave,tb2.usuario,tb2.senha,tb3.slug AS consulta_slug,tb3.nome AS nome_real,tb2.slug AS fornecedor, ';
            $query .= '(CASE WHEN (tb4.valor IS NOT NULL) THEN tb4.valor ELSE tb3.venda END) AS venda ';
            $query .= 'FROM `fornecedor_consulta` AS tbmain ';
            $query .= 'LEFT JOIN fornecedor AS tb2 ON tbmain.id_fornecedor_fk = tb2.id_fornecedor ';
            $query .= 'LEFT JOIN consulta AS tb3 ON tbmain.slug = tb3.slug ';
            $query .= 'LEFT JOIN cliente_consulta AS tb4 ON (tb3.id_consulta = tb4.id_consulta_fk AND id_cliente_fk = '.$id_cliente.' ) ';
            $query .= 'WHERE tbmain.slug = "'.$slug_consulta.'" ORDER BY custo ASC LIMIT 1';
            return $this->db->query($query);
        }else{
            return false;
        }
    }

    public function inserir_negativacao($dados=null){
        if ($dados != NULL) {
            $this->db->insert('negativacao', $dados);
            if ($this->db->affected_rows() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function atualizar_negativacao($id_negativacao=null,$dados=null){
        if($id_negativacao!=null&&$dados!=null):
            $this->db->update('negativacao', $dados, array('id_negativacao'=>$id_negativacao));
            if($this->db->affected_rows()>0){
                return true;
            }else{
                return false;
            }
        else:
            return false;
        endif;
    }

    public function retornar_id_ultima_negativacao($id_cliente){
        $this->db->where(array('id_cliente_fk'=>$id_cliente));
        $this->db->order_by('id_negativacao','DESC');
        $this->db->limit(1);
        $retorno = $this->db->get('negativacao');
        return $retorno->row()->id_negativacao;
    }

    public function retornar_produto_valores($id_cliente_fk=null){
        $this->db->where(array('id_cliente_fk'=>$id_cliente_fk));
        return $this->db->get('cliente_consulta');
    }

    public function retornar_produto_valores_array($id_cliente_fk=null){
        $consultas = $this->retornar_produto_valores($id_cliente_fk)->result();
        $retorno = array();
        foreach($consultas as $index => $consulta):
            $retorno[$consulta->id_consulta_fk] = $consulta;
        endforeach;
        return $retorno;
    }

    public function registrar_protocolo($dados=null){
        if($dados!=null):
            $this->db->insert('cliente_protocolo',$dados);
            if ($this->db->affected_rows() > 0) {
                return true;
            } else {
                return false;
            }
        else:
            return false;
        endif;
    }

    public function consultas_por_cliente($id_franquia,$inicio, $fim){
        $sql  = 'SELECT ';
        $sql .= 'UPPER(tb2.nome_ou_fantasia) AS nome, COUNT(*) AS qtd,ROUND(SUM(tbmain.valor),2) AS venda, ROUND(SUM(tbmain.custo_franquia),2) AS custo, ';
        $sql .= 'tb2.consultor ';
        $sql .= 'FROM `consulta_efetuada` AS tbmain ';
        $sql .= 'LEFT JOIN cliente AS tb2 ON tbmain.id_cliente_fk = tb2.id_cliente ';
        $sql .= 'WHERE ';
        $sql .= 'tbmain.criado_em BETWEEN "'.$inicio.'" AND "'.$fim.'" AND ';
        $sql .= 'tb2.id_franquia_fk = '.$id_franquia.' ';
        $sql .= 'GROUP BY tb2.nome_ou_fantasia ';

        return $this->db->query($sql);
    }

    public function veiculares_por_cliente($id_franquia,$inicio, $fim){
        $sql  = 'SELECT ';
        $sql .= 'UPPER(tb2.nome_ou_fantasia) AS nome, COUNT(*) AS qtd,ROUND(SUM(tbmain.valor),2) AS venda, ROUND(SUM(tbmain.custo_franquia),2) AS custo, ';
        $sql .= 'tb2.consultor ';
        $sql .= 'FROM `consulta_veicular_efetuada` AS tbmain ';
        $sql .= 'LEFT JOIN cliente AS tb2 ON tbmain.id_cliente_fk = tb2.id_cliente ';
        $sql .= 'WHERE ';
        $sql .= 'tbmain.criado_em BETWEEN "'.$inicio.'" AND "'.$fim.'" AND ';
        $sql .= 'tb2.id_franquia_fk = '.$id_franquia.' ';
        $sql .= 'GROUP BY tb2.nome_ou_fantasia ';

        return $this->db->query($sql);
    }

    public function negativacoes_por_cliente($id_franquia,$inicio, $fim){
        $sql  = 'SELECT ';
        $sql .= 'UPPER(tb2.nome_ou_fantasia) AS nome, COUNT(*) AS qtd,ROUND(SUM(tbmain.valor),2) AS venda, ROUND(SUM(tbmain.custo_franquia),2) AS custo, ';
        $sql .= 'tb2.consultor ';
        $sql .= 'FROM `negativacao` AS tbmain ';
        $sql .= 'LEFT JOIN cliente AS tb2 ON tbmain.id_cliente_fk = tb2.id_cliente ';
        $sql .= 'WHERE ';
        $sql .= 'tbmain.criado_em BETWEEN "'.$inicio.'" AND "'.$fim.'" AND ';
        $sql .= 'tb2.id_franquia_fk = '.$id_franquia.' ';
        $sql .= 'GROUP BY tb2.nome_ou_fantasia ';

        return $this->db->query($sql);
    }

    public function baixas_por_cliente($id_franquia,$inicio, $fim){
        $sql  = 'SELECT ';
        $sql .= 'UPPER(tb2.nome_ou_fantasia) AS nome, COUNT(*) AS qtd,ROUND(SUM(tbmain.valor),2) AS venda, ROUND(SUM(tbmain.custo_franquia),2) AS custo, ';
        $sql .= 'tb2.consultor ';
        $sql .= 'FROM `negativacao_baixa` AS tbmain ';
        $sql .= 'LEFT JOIN cliente AS tb2 ON tbmain.id_cliente_fk = tb2.id_cliente ';
        $sql .= 'WHERE ';
        $sql .= 'tbmain.criado_em BETWEEN "'.$inicio.'" AND "'.$fim.'" AND ';
        $sql .= 'tb2.id_franquia_fk = '.$id_franquia.' ';
        $sql .= 'GROUP BY tb2.nome_ou_fantasia ';

        return $this->db->query($sql);
    }

    public function cartas_por_cliente($id_franquia,$inicio, $fim){
        $sql  = 'SELECT ';
        $sql .= 'UPPER(tb2.nome_ou_fantasia) AS nome, COUNT(*) AS qtd,ROUND(SUM(tbmain.valor_carta),2) AS venda, ROUND(SUM(tbmain.custo_franquia),2) AS custo, ';
        $sql .= 'tb2.consultor ';
        $sql .= 'FROM `carta` AS tbmain ';
        $sql .= 'LEFT JOIN cliente AS tb2 ON tbmain.id_cliente_fk = tb2.id_cliente ';
        $sql .= 'WHERE ';
        $sql .= 'tbmain.criado_em BETWEEN "'.$inicio.'" AND "'.$fim.'" AND ';
        $sql .= 'tb2.id_franquia_fk = '.$id_franquia.' ';
        $sql .= 'GROUP BY tb2.nome_ou_fantasia ';

        return $this->db->query($sql);
    }

    public function clientes_pastas(){
        return $this->db->get('cliente_pasta');
    }

    public function cliente_pasta_cadastrar($dados){
        if ($dados != NULL) {
            $this->db->insert('cliente_pasta', $dados);
            if ($this->db->affected_rows() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function cliente_pasta_subs($id_pasta){
        $sql = 'SELECT tbmain.*,UPPER(tb2.nome_ou_fantasia) AS nome,UPPER(tb2.razao_social) AS razao_social FROM cliente_pasta_sub AS tbmain LEFT JOIN cliente AS tb2 ON tbmain.id_cliente_fk = tb2.id_cliente WHERE tbmain.id_cliente_pasta_fk = '.$id_pasta;
        return $this->db->query($sql);
    }

    public function cliente_pasta_sub_cadastrar($dados){
        if ($dados != NULL) {
            $this->db->insert('cliente_pasta_sub', $dados);
            if ($this->db->affected_rows() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function cliente_pasta_boletos_de_ids($array_ids=null,$inicio = null, $fim = null){
        if($array_ids!=null){
            $ids = '';
            foreach($array_ids as $i => $a): if($i>0){ $ids .= ','; } $ids .= $a; endforeach;
            $sql = 'SELECT * FROM boleto WHERE id_cliente_fk IN ('.$ids.') AND data_vencimento BETWEEN "'.$inicio.'" AND "'.$fim.'"';
            return $this->db->query($sql);
        }else{
            return null;
        }
    }
}