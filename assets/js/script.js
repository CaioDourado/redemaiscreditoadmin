var url_base = "https://sistemaadm.redemaiscredito.com.br/index.php/";

$(function(){
    $(".cep").keyup(function(){
        let conteudo = $(this).val();
        if(conteudo.length===8){
            $.get( "https://viacep.com.br/ws/"+conteudo+"/json/", function( retorno ) {
                $(".logradouro").val(retorno.logradouro);
                $(".cidade").val(retorno.localidade);
                $(".bairro").val(retorno.bairro);
                $(".uf").val(retorno.uf);
            });
        }
    });

    $(".data").mask('00/00/0000');
    $(".hora").mask('00:00:00');
    $(".cpf").mask('000.000.000-00', {reverse: true});
    $(".telefone").mask('(00) 0000-0000');
    $(".celular").mask('(00) 00000-0000');
    $(".dinheiro").mask("#.##0,00", {reverse: true});

    $(".cpfcnpj").keydown(function(){
        try {
            $(".cpfcnpj").unmask();
        } catch (e) {}

        var tamanho = $(".cpfcnpj").val().length;

        if(tamanho < 11){
            $(".cpfcnpj").mask("999.999.999-99");
        } else {
            $(".cpfcnpj").mask("99.999.999/9999-99");
        }

        // ajustando foco
        var elem = this;
        setTimeout(function(){
            // mudo a posição do seletor
            elem.selectionStart = elem.selectionEnd = 10000;
        }, 0);
        // reaplico o valor para mudar o foco
        var currentValue = $(this).val();
        $(this).val('');
        $(this).val(currentValue);
    });

    $(".btn-loading").click(function(){
        $(".tela-loading").show();
    });

    $("#consulta_cheque").change(function(){
       let valor = $(this).val();
       if(valor==="1"){
           $("#campos_consulta_cheque").show();
       }else{
        $("#campos_consulta_cheque").hide();
       }
    });

    $(".cmc7").on("paste",function(event){
        let val = event.originalEvent.clipboardData.getData('Text');
        $(".cmc7:eq(0)").val(val.substring(0,8));
        $(".cmc7:eq(1)").val(val.substring(8,18));
        $(".cmc7:eq(2)").val(val.substring(18,30));
    });

    $(".check_line").click(function(){
        if($(this).is(":checked")){
            $(this).parent().parent().find('input:text').prop('disabled',false);
        }else{
            $(this).parent().parent().find('input:text').prop('disabled',true);
        }
    });

    $("#bt_enviar_emails").click(function(){
        enviar_emails()
    });

	$('.multiselect').multiselect({
		nonSelectedText: 'Selecionar Opções',
		enableFiltering: true,
		enableCaseInsensitiveFiltering: true,
		buttonWidth:'100%'
	});
});

function enviar_emails(){
    let objs = $(".checkbox_email:checked");
    if(objs.length>0){
        let check_1 = objs[0];
        let url = url_base+"boleto/enviar_email_ajax/"+$(check_1).attr("hash");
        $.get( url , function( retorno ) {
            retorno = JSON.parse(retorno);
            if(retorno.status=="sucesso"){
                $(check_1).parent().parent().find('.status_envio').removeClass("text-info").addClass("text-success").text("Enviado");
            }else{
                $(check_1).parent().parent().find('.status_envio').removeClass("text-info").addClass("text-danger").text("Erro Ao Enviar");
            }
            $(check_1).prop( "checked", false );
            setTimeout(function(){ enviar_emails(); },3);
        });
    }
}

function check_all(el){
	let get_check = el.checked;
	$("input[type=checkbox]").each(function(){
		$(this).prop('checked',get_check);
	});
}
