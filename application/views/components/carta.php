<div class="carta">
    <div class="header">
        <h4><b>Para uso do Correio</b></h4>
        <table>
            <tbody>
                <tr>
                    <td>
                        <p>( ) Mudou-se</p>
                        <p>( ) Endereço Insuficiente</p>
                        <p>( ) Não Existe o Número Indicado</p>
                        <p>( ) Desconhecido</p>
                        <p>( ) Recusado</p>
                    </td>
                    <td>
                        <p>( ) Não Procurado</p>
                        <p>( ) Ausente</p>
                        <p>( ) Falecido</p>
                        <p>( ) Informação escrita pelo Síndico</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="corpo">
        <img src="http://localhost/consulta/assets/imgs/logo_redemaiscredito_pretoebranco.png" alt="" class="logo" style="margin-top: 100px;">
        <div class="campo_dados">
            <p><b><?php echo $nome; ?></b></p>
            <p><?php echo $endereco; ?></p>
            <p><?php echo $cep; ?> - <?php echo $bairro; ?></p>
            <p><?php echo $cidade; ?> - <?php echo $uf; ?></p>
        </div>
        <div class="dados_finais">
            <p>REDE MAIS CRÉDITO SISTEMAS INTERLIGADOS - ME</p>
            <p>Rua Fernando Lobo 232</p>
            <p>Centro - Juiz de Fora - MG - 36016-230</p>
            <p>www.redemaiscredito.com.br - cac@redemaiscredito.com.br</p>
        </div>
    </div>
</div>
<pagebreak />
<div class="carta fundo_back">
    <div class="sobreposicao">
        <img src="http://localhost/consulta/assets/imgs/logo_redemaiscredito_pretoebranco.png" alt="" class="minilogo">
        <h4 style="text-align: center;">Carta de Comunicado Extra-Judicial</h4>
        <p>Juiz de Fora, <?php echo date('d') ?> de <?php $meses = meses_array();echo $meses[date('m')] ?> de <?php echo date('Y'); ?></p>
        <br>
        <p>Prezado(a) Sr(a),</p>
        <p><?php echo $nome; ?></p>
        <p>CPF: <?php echo $cpf_cnpj; ?></p>
        <br>
        <p>Vimos pela presente informar a V.Sa. que se encontra em aberto, junto à nossa empresa, um débito não quitado representado por:</p>
        <br>
        <p><b>Empresa Credora:</b> <?php echo $empresa; ?></p>
        <p><b>Telefone:</b> <?php echo $telefone; ?></p>
        <p><b>Data Ocorrência:</b> <?php echo $data_ocorrencia; ?></p>
        <p><b>Valor do Débito:</b> <?php echo $valor_debito; ?></p>
        <p><b>Natureza do Débito:</b> </p>
        <p><b>Dados da Dívida:</b> <?php echo $titulo; ?></p>
        <br>
        <p>Tendo em vista que até o presente momento não acusamos o recebimento da referida prestação, solicitamos a V.Sa a quitação desta ou seu comparecimento em nosso estabelecimento a fim de regularizar tal pendência no prazo máximo de 10 dias corridos.</p>
        <br>
        <p>Informamos, ainda, que a não regularização da referida pendência no prazo acima estabelecido ensejará  tomada da(s) seguinte(s) providência(s) legal(is).</p>
        <br>
        <p><small>-Encaminhamento de vosso nome aos bancos de dados de proteção ao crédito SCPC, SPC Brasil, SERASA e Rede Mais Credito Sistemas Interligados.</small></p>
        <p><small>-Protesto de Título</small></p>
        <p><small>-Propositura da compentente ação judicial para o recebimento do valor principal, acrescido de juros legais, mora além dos honorários advocatícios.</small></p>
        <br>
        <p>Desta forma, a fim de se evitar tal situação, reiteramos o pedido no sentido de que V.Sa. promova a quitação da referida dívida ou o seu comparecimento em nosso estabelecimento no prazo máximo de 10 dias, a fim de resolvermos tal pendência.</p>
        <div class="parte_final">
            <p>Informamos ainda que V.Sa encontra-se constitúido em mora para todos os efeitos legais a partir do recebimento da presente notificação, conforme os termos do artigo 397 da lei 10.406/02 - Código Civil Brasileiro</p>
            <br>
            <p>Solicitamos a V.Sa desconsiderar a presente, caso o referido débito já tenha sido quitado.</p>
            <div class="respeitosamente">
                Respeitosamente
                <br>
                REDE MAIS CREDITO SISTEMAS INTERLIGADOS - ME
            </div>
        </div>
    </div>
</div>