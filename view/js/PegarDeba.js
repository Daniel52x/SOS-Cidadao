
var paginacao = 1;
var validar = 0 // se for 0 roda o jaquinha se for outro valor não roda
var teste = false;
    function jaquinha(){
        var jaq;
    
        $.ajax({
            url: '../PegarDebates.php',
            type: "get",
            data: "pagina="+paginacao,
            success: function(data){
                if(data =="Maior"){ //Maior significa que não teve resultado para mostrar
                    validar = 1 //então nao vamos mais rodar o jaquinha, pois chegamos ao final de todas as reclamações
                    //alert("chegou no fim")
                   
                }else{//caso o resultado for outro roda normal e adiciona na paginação
                    paginacao++ 
                    $("#pa").append("<div style=' display:flex; justify-content:center; width:100%'>\
                    <img src='imagens/gif2.gif' id='loader'></div>"); // adicionar a estrutura do gif no final da ultima publicação do momento no html
                    $(window).scrollTop($(document).height()); // descer o scroll pro final
                    setTimeout(function(){ //simular delay de carregamento
                        $('#loader').remove();//remove a estrutura do gif do html
                        teste2(data); //manda ver na criação de conteudo
                    },1780); // tempo do delay

                    setTimeout(function(){ //simular delay de carregamento
                        teste = false;
                    },2000);
                    teste = true;
                   
                }            
                //$('#lista').html(data);
                
            }        
    
        });   
    }



$(document).ready(function(){
    $(window).scroll(function() {
        if($(window).scrollTop() == $(document).height() - $(window).height()) {
            if(validar == 0){ // valida se roda o jaquinha ou não baseado no valor da vaiavel validar
                if(teste == false){
                    jaquinha()
                }
            }else{
              
            }        

        }
        
    });
});


function teste2(resposta){
    var arr1 = JSON.parse(resposta);   
    
    var mensa = "";
    for(contador = 0; contador < arr1.length; contador++){
                mensa += '<div class="item-publicacao">\
                <div class="item-topo">\
                    <a href="perfil_debate.php?ID='+arr1[contador]['cod_usu']+'">\
                        <div>\
                            <img src="../Img/perfil/'+arr1[contador]['img_perfil_usu']+'">\
                        </div>\
                        <p><span class="negrito">'+arr1[contador]['nome_usu']+'</a></span><time>'+arr1[contador]['dataHora_deba']+'</time></p>\
                        <div class="mini-menu-item">\
                            <i class="icone-3pontos"></i>\
                            <div><!--DA PRA TIRAR A DIV-->\
                                <ul>';
                                if(arr1[contador]["indDenun"] == true){
                                    mensa += '<li><i class="icone-bandeira"></i><span class="negrito">Denunciado</span></li>';
                                }else if(arr1[contador]["indDenun"] == false){ // nao denunciou\
                                    mensa += '<li class="denunciar-item"><a href="#"><i class="icone-bandeira"></i>Denunciar</a></li>';
                                }

                                if(arr1[contador]["LinkApagar"] != false && arr1[contador]["LinkUpdate"]){ // Denuncioou
                                    mensa += '<li><a href='+arr1[contador]["LinkApagar"]+'><i class="icone-fechar"></i></i>Remover</a></li>';                                            
                                    mensa += '<li><a href='+arr1[contador]["LinkUpdate"]+'><i class="icone-edit-full"></i></i>Alterar</a></li>';
                                }                                       
                            mensa+='</ul>\
                            </div>';
                            if(arr1[contador]["indCarregarModalDenun"] == true){ // so quero q carregue em alguns casos?>
                               mensa+='<div class="modal-denunciar">\
                                    <div class="modal-denunciar-fundo"></div>\
                                    <div class="box-denunciar">\
                                        <div>\
                                            <h1>Qual o motivo da denuncia?</h1>\
                                            <span class="fechar-denuncia">&times;</span>\
                                        </div>\
                                        <form form method="post" action="../DenunciarDebate.php">\
                                            <textarea placeholder="Qual o motivo?" id="motivo" name="texto"></textarea>\
                                            <input type="hidden" name="id_deba" value="'+arr1[contador]['cod_deba']+'">';                
                                mensa+='<button type="submit"> Denunciar</button>\
                                        </form>';                                       
                                mensa +='</div>\
                                </div>';
                            }
                    mensa+='</div>\
                    </div>\
                    <a href="Pagina-debate.php?ID='+arr1[contador]['cod_deba']+'">\
                        <figure>\
                            <img src="../Img/debate/'+arr1[contador]['img_deba']+'">\
                        </figure>\
                        <div class="legenda">\
                            <p>'+arr1[contador]['nome_deba']+'</p><p>'+arr1[contador]['qtdParticipantes']+'</p><i class="icone-grupo"></i>\
                        </div>';                        
                    mensa += '</a>\
            </div>';
    }

    $(document).ready(function(){
        jQuery(function($){
        
          $(".icone-3pontos").click(function(){
            var $this = $(this);
            $this.parent().toggleClass('mini-menu-item-ativo')
          })
        });

        jQuery(function($){
            /* abrir quando */
            $(".denunciar-item").click(function(){
             
              $(this).parents(":eq(2)").find("div.modal-denunciar").addClass("modal-denunciar-ativo");
              $("body").css("overflow","hidden")
            })
            /* fechar quando clicar fora*/
            $(".modal-denunciar-fundo").click(function(){
              $(this).parent().removeClass("modal-denunciar-ativo");
              $("body").css("overflow","auto")
            })
            /* fechar quando clicar no X*/
            $(".fechar-denuncia").click(function(){
              $(this).parents(":eq(2)").removeClass("modal-denunciar-ativo");
              $("body").css("overflow","auto")
            })
          })
      });
    //document.getElementById("pa").innerHTML = mensa;
    $("#pa").append(mensa);
    teste = true;
}
