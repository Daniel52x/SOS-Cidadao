
var paginacao = 0

setTimeout(function(){

    $(document).ready(function(){



        //$//("#pa").scrollTop($("#pa")[0].scrollHeight);
        $('#pa').scrollTop($("#pa")[0].scrollHeight);
        // Assign scroll function to chatBox DIV
        $('#pa').scroll(function(){
           if ($('#pa').scrollTop() == 0){
             
               // $('#loader').show();
         
           
        
               paginacao--;
               //Simulate server delay;
               setTimeout(function(){
                

                $.ajax({
                    
                    url: '../PegarMensagens.php',
                    type: "get",
                    data: "pagina="+paginacao+"&ID=30",
                    success: function(data){            
                        //$('#lista').html(data);
                        //$("#pa").prepend(data); 
                        teste3(data);
                    }
                            
            
                }); 

                   $('#pa').scrollTop(30);
                   alert("ta no topo")
               },780); 
           }
        });
        
        })
        
        
},1000); 

function teste3(resposta){
    var arr1 = JSON.parse(resposta);   
    
    document.getElementById('ImgDeba').innerHTML = "<img  src='../Img/debate/"+arr1[0]['dadosDeba'][0]['img_deba']+"'>";   
    var contador;
    mensa2 = "";
    for(contador=0; contador < arr1[2]['debateQParticipa'].length; contador++){
        mensa2 += "<div class=contatinhos><a href='debate_mensagens.php?ID="+arr1[2]['debateQParticipa'][contador]['cod_deba']+"&pagina=ultima'><div class=img-debate><img src='../Img/debate/"+arr1[2]['debateQParticipa'][contador]['img_deba']+"' alt=debate></div></a><div class=status-debate><p>"+arr1[2]['debateQParticipa'][contador]['nome_deba']+"</p></div><div class=info-contatinho><div class=data_mensagem><p>"+arr1[2]['debateQParticipa'][contador]['dataHora_deba']+"</p></div>";
        if(arr1[2]['debateQParticipa'][contador]['quantidade'] > 0 ) {
            mensa2 +="<div class=qtd_mensagens><p>"+arr1[2]['debateQParticipa'][contador]['quantidade']+"</p></div>";
        }
        mensa2 += "</div></div>"
    }
    mensa = ""; 
    
    for(contador=0; contador < arr1[3]['mensagens'].length; contador++){
        classe = arr1[3]['mensagens'][contador]['classe'];
        var msg_id = arr1[3]['mensagens'][contador]['cod_mensa'];

            mensa += "<div class=" + classe + ">";
        
        if(classe == 'linha-mensagem_padrao'){
            ultimo_id = msg_id;
            mensa += "<div class=usuario-msg-foto><img src='../Img/perfil/"+arr1[3]['mensagens'][contador]['img_perfil_usu']+"'></div><div class=mensagem_padrao><span class=nome><a href=perfil_reclamacao.php?ID="+arr1[3]['mensagens'][contador]['cod_usu']+">"+arr1[3]['mensagens'][contador]['nome_usu']+"</a></span>";
        }else{
            mensa += "<div>";
        }
            mensa += "<span>"+arr1[3]['mensagens'][contador]['texto_mensa'];
        if(classe == 'linha-mensagem_sistema') { 
            ultimo_id = msg_id;
            mensa += "<span>"+arr1[3]['mensagens'][contador]['hora']+"</span>"
        }else{
            ultimo_id = msg_id;
            mensa += "<sub>"+arr1[3]['mensagens'][contador]['hora']+"</sub>";
        }
            mensa += "</span></div></div>";
        }
            
         //alert(ultimo_id)  
    
    
    //document.getElementsByClassName('mensagens').innerHTML = "";    
    document.getElementById('contatosJs').innerHTML = mensa2;
   // document.getElementById('pa').innerHTML = mensa;
   $("#pa").prepend(mensa);
}