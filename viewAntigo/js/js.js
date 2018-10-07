jQuery(function($)
{
  function fechar(){
    $(".user-menu").css("width","0");
  }
  
  $("#abrir").click(function()
  {
    var tela= $(document).width();
    if(tela<=480){
      $(".user-menu").css("width","100vw");
      $(window).resize(fechar);
    }else{
      $(".user-menu").css("width","450px");
      $(window).resize(fechar);
    }
  })
  $(".fechar").click(fechar);
  
  
})

jQuery(function($)
{
  
  var click = 0;
  $("#abrir-not").click(function(){
    $(".notificacoes").toggleClass('ativo');
    var banana= $(".notificacoes").attr("class");
    
    if(banana=='notificacoes ativo'){
      click++;
      $("#not-fechado").html(' fechou ' + click + 'vezes');
      
    }
    
  })
  
  $(window).resize(function(){
    $(".notificacoes").removeClass('ativo');
  })     
  
})


jQuery(function($) {
  
  $(document).ready(function(){
    /*ativar*/
    var leftPos, newWidth, $linha;
    
    $('.espacos').append("<li id='linha'></li>");//adiciona dentro das abas a seguinte tag
    $linha = $('#linha');
    $linha.width($('.ativo').width()) //pegar largura do elemento que esta ativo agora
    .css('left', $('.ativo a').position().left) // o valor da posição left vai ser o valor do a que esta ativo no nomento
    .data('origLeft', $linha.position().left)// armazenar os valores da position left do elemento que esta selecionado inicialmente
    .data('origWidth', $linha.width());// armazenar o valor da largura do elemento que esta selecionado inicialmente
    
    /* trocar cor do a ativo */
    $('.espacos li a').click(function() {
      var $this = $(this); //o elemento que vai ser clicado
      $this.parent().addClass('ativo').siblings().removeClass('ativo');// vai pegar o elemento pai do a que acabu de ser clicado e mandar a classe ativo para ele em seguida remover a classe ativo dos elementos que não estiver selecionado
      $linha
      .data('origLeft', $this.position().left) //armazena o valor da posição left elemento que esta sendo clicado
      .data('origWidth', $this.parent().width());//armazena o valor do width do elemento que esta sendo clicado
      //return false; // encerra
    });
    
    /*mudar a linha para o ativo*/
    
    $('.espacos li').find('a').click(function() { //econtrar o a dentro do li que estamos clicando e execultar a função
      var $thisBar = $(this); //this se refere ao <a>  confirmo execultando isso, alert($thisBar.attr('href'))
      leftPos = $thisBar.position().left; // a posição do elemento a que clicamos
      newWidth = $thisBar.parent().width();// a largura do elemento...
      $linha.css({ // posicionar no css a linha
        "left": leftPos,// daqui para baixo já sabemos...
        "width": newWidth
      });
    }, function() {
      $linha.css({
        "left": $linha.data('origLeft'),
        "width": $linha.data('origWidth')
      });
    });
  });
});


jQuery(function($){
  
  $(".icone-3pontos").click(function(){
    var $this = $(this);
    $this.parent().toggleClass('mini-menu-item-ativo')
  })
})



//VERIFICAÇÃO DOS FORM
jQuery(function(){
  $(".formulario").submit(function(){
    
    var titulo = $("#titulo").val();
    
    if( titulo === ""){
      $("#titulo").parent().find('p').text("este campo não pode ser vazio");
      $("#titulo").parent().find('span').addClass('verificar');
      $("#titulo").css("background" , 'rgba(256,000,000,.1)' )
      return false;
    }else{
      $("#titulo").parent().find('p').text("");
      $("#titulo").parent().find('span').removeClass('verificar');
      $("#titulo").css("background" , 'white' )
    }
  });
  $(".formulario").submit(function(){
    
    var tema = $("#tema").val();
    
    if( tema === ""){
      $("#tema").parent().find('p').text("este campo não pode ser vazio ");
      $("#tema").parent().find('span').addClass('verificar');
      $("#tema").css("background" , 'rgba(256,000,000,.1)' )
      return false;
    }else{
      $("#tema").parent().find('p').text("");
      $("#tema").parent().find('span').removeClass('verificar');
      $("#tema").css("background" , 'white' )
    }
    
  });
  
  $(".formulario").submit(function(){
    var imgDebate = $("#imagemDebateInput").val();
    
    if(imgDebate == ""){
      $(".imagem").find('p:last-child').text("uma imagem é obrigatoria");
      return false;
    }
  })
  
  $(document).on("change", "#imagemDebateInput", function(){
    var InputData = document.getElementById('imagemDebateInput');
    var caminhoImagem = InputData.value;
    // verificando a extensão
    var extensao = caminhoImagem.substring(
      caminhoImagem.lastIndexOf('.') + 1).toLowerCase();
      //verificando se é uma img
      if (extensao == "gif" || extensao == "png" || extensao == "bmp"
      || extensao == "jpeg" || extensao == "jpg") {
        // vai mostrar no preview
        if (InputData.files && InputData.files[0]) {
          var reader = new FileReader();
          reader.onload = function(e) {
            $('#imgPreview').attr('src', e.target.result);
            $(".imagem").find('p:last-child').text("");
          }
          reader.readAsDataURL(InputData.files[0]);
        }
      } 
      //se não for uma imagem
      else {
        $('#imagemDebateInput').val("");
        $(".imagem").find('p:last-child').text("esse formato não é valido");
        return false;
      }
    })
    
    
    $(".formulario").submit(function(){
      
      var sobre = $("#sobre").val();
      
      if( sobre === ""){
        $("#sobre").parent().find('p').text("este campo não pode ser vazio ");
        $("#sobre").css("background" , 'rgba(256,000,000,.1)' )
        return false;
      }else{
        $("#sobre").parent().find('p').text("");
        $("#sobre").css("background" , 'white' )
        
      }
      
    });
    
    $(".formulario").submit(function(){
      
      if($('input[name=categoria]:checked').length<=0)
      {
        $(".categorias").find('p').text("Escolha uma catgoria")
      }else{
        $(".categorias").find('p').text("")
      }
      
    });
    
    
    $(".formulario").submit(function(){
      
      //Nova variável "cep" somente com dígitos.
      var CEP = $("#cep").val().replace(/\D/g, '');
      
      
      if( CEP === ""){
        $("#cep").parent().find('p').text("O CEP é obrigatorio");
        $("#cep").parent().find('span').addClass('verificar');
        $("#cep").css("background" , 'rgba(256,000,000,.1)' )
        return false;
        
      }else{
        
        
        /*
        function limpa_formulário_cep() {
          // Limpa valores do formulário de cep.
          $("#local").val("");
          $("#bairro").val("");
        }
        //Nova variável "cep" somente com dígitos.
        var cep = CEP;
        
        //Verifica se campo cep possui valor informado.
        if (cep != "") {
          
          //Expressão regular para validar o CEP.
          var validacep = /^[0-9]{8}$/;
          
          //Valida o formato do CEP.
          if(validacep.test(cep)) {
            
            //Preenche os campos com "..." enquanto consulta webservice.
            $("#local").val("...");
            $("#bairro").val("...");
            
            
            //Consulta o webservice viacep.com.br/
            $.getJSON("https://viacep.com.br/ws/"+ cep +"/json/?callback=?", function(dados) {
            
            if (!("erro" in dados)) {
              //Atualiza os campos com os valores da consulta.
              $("#local").val(dados.logradouro);
              $("#bairro").val(dados.bairro);
              
            } //end if.
            else {
              //CEP pesquisado não foi encontrado.
              limpa_formulário_cep();
              $("#cep").parent().find('p').text("CEP não encontrado");
              $("#cep").parent().find('span').addClass('verificar');
              $("#cep").css("background" , 'rgba(256,000,000,.1)' )
              
              $("#bairro").parent().find('p').text("CEP não encontrado");
              $("#bairro").parent().find('span').addClass('verificar');
              $("#bairro").css("background" , 'rgba(256,000,000,.1)' )
              
              $("#local").parent().find('p').text("CEP não encontrado");
              $("#local").parent().find('span').addClass('verificar');
              $("#local").css("background" , 'rgba(256,000,000,.1)' )
            }
          });
        } //end if.
        else {
          //cep é inválido.
          limpa_formulário_cep();
          $("#cep").parent().find('p').text(" o CEP é invalido");
          $("#cep").parent().find('span').addClass('verificar');
          $("#cep").css("background" , 'rgba(256,000,000,.1)' )
          
        }
      } //end if.
      */
      
      
      $("#cep").parent().find('p').text("");
      $("#cep").parent().find('span').removeClass('verificar');
      $("#cep").css("background" , 'white' )
      $("#cep").val(CEP.replace('-',''))
      
      
    }
    
  });
  
  $(document).ready(function(){
    $("#cep").mask("99999-999");
  });
  
  $(".formulario").submit(function(){
    
    var local = $("#local").val();
    
    if( local === "" ){
      $("#local").parent().find('p').text("este campo não pode ser vazio ");
      $("#local").parent().find('span').addClass('verificar');
      $("#local").css("background" , 'rgba(256,000,000,.1)' )
      return false;
    }else{
      $("#local").parent().find('p').text("");
      $("#local").parent().find('span').removeClass('verificar');
      $("#local").css("background" , 'white' )
      
    }
    
  });
  
  $(".formulario").submit(function(){
    
    var bairro = $("#bairro").val();
    
    if( bairro === "" ){
      $("#bairro").parent().find('p').text("este campo não pode ser vazio ");
      $("#bairro").parent().find('span').addClass('verificar');
      $("#bairro").css("background" , 'rgba(256,000,000,.1)' )
      return false;
    }else{
      $("#bairro").parent().find('p').text("");
      $("#bairro").parent().find('span').removeClass('verificar');
      $("#bairro").css("background" , 'white' )
      
    }
    
  });
  
  $(".formulario").submit(function(){
    var img = $("#imagem").val();
    
    if(img == ""){
      
    }
  })
  
  
  
  $(".formulario").submit(function(){
    var imgDebate = $("#imagem").val();
    
    if(imgDebate == ""){
      $(".imagem").find('p:last-child').text("");
    }
  })
  
  $(document).on("change", "#imagem", function(){
    var InputData = document.getElementById('imagem');
    var caminhoImagem = InputData.value;
    // verificando a extensão
    var extensao = caminhoImagem.substring(
      caminhoImagem.lastIndexOf('.') + 1).toLowerCase();
      //verificando se é uma img
      if (extensao == "gif" || extensao == "png" || extensao == "bmp"
      || extensao == "jpeg" || extensao == "jpg") {
        // vai mostrar no preview
        if (InputData.files && InputData.files[0]) {
          var reader = new FileReader();
          reader.onload = function(e) {
            $('#imgPreview').attr('src', e.target.result);
            $(".imagem").find('p:last-child').text("");
          }
          reader.readAsDataURL(InputData.files[0]);
        }
      } 
      //se não for uma imagem
      else {
        $('#imagem').val("");
        $(".imagem").find('p:last-child').text("esse formato não é valido");
        return false;
      }
    })
    
    
    
    
    
  })
  
  jQuery(function($){
    $("#abrir-debate-info").click(function(){
      $(".usuarios-debate-info").addClass("usuarios-debate-info-ativo")
      $(".mini-menu-item").removeClass("mini-menu-item-ativo")
    })
    
    $("#fechar-debate-info").click(function(){
      $(".usuarios-debate-info").removeClass("usuarios-debate-info-ativo")
    })
  })
  
  jQuery(function($){
    $("#abrir-contatos").click(function(){
      $(".contatos").addClass("contatos-ativo");
      $(".mini-menu-item").removeClass("mini-menu-item-ativo")
    })
    $("#fechar-contatos").click(function(){
      $(".contatos").removeClass("contatos-ativo")
    })
  })
  
  jQuery(function($){
    $(".icone-filtro").click(function(){
      $(this).parent().find("form").addClass("filtro-ativo")
    })
    $("#fechar-filtro").click(function(){
      $(this).parent().removeClass("filtro-ativo")
    })
  })
  
  
  jQuery(function($){
    $(".tabelinha-linha").click(function(){
      var $this = $(this);
      var classe = $this.attr('class');
      var motivo = $this.find('div.motivo').attr("class");
     
      /*criar uma rotina para remover os outros menu aberto */

      if(motivo == "motivo motivo-ativo"){ /* verificar de o motivo ta ativo, se for verdade ele não abre o mini menu */
      }else{
        if(classe == "mini-menu-adm"){/* se não se, se a classe or igual a: execulta */
         
          $(this).find("td:nth-child(1)").find("div").toggleClass("mini-menu-adm-ativo");
        }else{ /* se não isso */
          $(this).siblings().find("td:nth-child(1) div").removeClass("mini-menu-adm-ativo"); /* pegar os elementos que não corresponde aesse que esta sendo clicado, e remover a class*/
          $(this).find("td:nth-child(1) div").toggleClass("mini-menu-adm-ativo"); /*  */
          

        }
      }
      
      
    })
    $(".mini-menu-adm").click(function(){
      $(this).find("td:nth-child(1)").find("div").removeClass("mini-menu-adm-ativo")
    })
  })
  
  
  jQuery(function($){
    /*ativar quando clicar e remover tag de ativar */
    $(".motivo-ativar").click(function(){
      $(this).parents(":eq(3)").find("div.motivo").addClass("motivo-ativo")
      $(this).parents(":eq(4)").removeClass("tabelinha-linha");
    })
    /*fechar quando clicar e colocar de novo tag de ativar*/
    $(".fechar-motivo").click(function(){
      $(this).parents(":eq(2)").removeClass("motivo-ativo");
      $(this).parents(":eq(4)").addClass("tabelinha-linha")
    })
    /*fechar quando clicar fora e adicionar class de ativar */
    $(".motivo-fundo").click(function(){
      $(this).parent().removeClass("motivo-ativo");
      $(this).parents(":eq(2)").addClass("tabelinha-linha")
    })
  })
  

  jQuery(function($){
    /* abrir quando */
    $(".denunciar-item").click(function(){
      $(this).parents(":eq(2)").find("div.modal-denunciar").addClass("modal-denunciar-ativo");
    })
    /* fechar quando clicar fora*/
    $(".modal-denunciar-fundo").click(function(){
      $(this).parent().removeClass("modal-denunciar-ativo");
    })
    /* fechar quando clicar no X*/
    $(".fechar-denuncia").click(function(){
      $(this).parents(":eq(2)").removeClass("modal-denunciar-ativo");
    })
  })

/* modal desativar */

  jQuery(function($){

    $(".desativar-btn").click(function(){
      $(".modal-desativar").addClass("modal-desativar-ativo");
    })
    /* fechar quando clicar fora*/
    $(".modal-desativar-fundo").click(function(){
      $(this).parent().removeClass("modal-desativar-ativo");
    })
    /* fechar quando clicar no X*/
    $(".fechar-desativar").click(function(){
      $(this).parents(":eq(2)").removeClass("modal-desativar-ativo");
    })
  })

  /* modal erro php */

  jQuery(function($){

    /* fechar quando clicar fora*/
    $(".modal-erro-fundo").click(function(){
      $(this).parent().remove();
    })
    /* fechar quando clicar no X*/
    $(".fechar-erro").click(function(){
      $(this).parents(":eq(2)").remove();
    })
  })

  /* verificação login */

  jQuery(function($){
    $("#login").submit(function(){
      var senha = $("#senha").val();
      if( senha === ""){
        $("#senha").parent().find("label").css("background-color" , 'rgba(256,000,000)');
        $("#senha").css("border-color" , 'rgba(256,000,000)');
        $("#senha").focus();
        return false;
      }else{
        $("#senha").parent().find("label").css("background-color" , 'dodgerblue' );
        $("#senha").css("border-color" , 'dodgerblue');
      }
    });

    $("#login").submit(function(){
      var email = $("#email").val();
      if( email === ""){
        $("#email").parent().find("label").css("background-color" , 'rgba(256,000,000)');
        $("#email").css("border-color" , 'rgba(256,000,000)');
        $("#email").focus();
        return false;
      }else{
        $("#email").parent().find("label").css("background-color" , 'dodgerblue' );
        $("#email").css("border-color" , 'dodgerblue');
      }
    });


    $("#login").submit(function(){
      var email = $("#email").val();
      var senha = $("#senha").val();

      if( email === "" && senha === ""){
        $(".aviso-form-inicial").show();
        $(".aviso-form-inicial").find("p").text("O email e a senha então vazios")
      }else if( senha === ""){
        $(".aviso-form-inicial").show();
        $(".aviso-form-inicial").find("p").text("você precisa digitar um senha")
      }else{
        $(".aviso-form-inicial").show();
        $(".aviso-form-inicial").find("p").text("você precisa digitar uma E-mail")
      }
    })
  })


    /* verificação cadastro */

    jQuery(function($){


      $("#cadastro").submit(function(){
        var senhaC = $("#senhaC").val();
        if( senhaC === ""){
          $("#senhaC").parent().find("label").css("background-color" , 'rgba(256,000,000)');
          $("#senhaC").css("border-color" , 'rgba(256,000,000)');
          $("#senhaC").focus();
          return false;
        }else{
          $("#senhaC").parent().find("label").css("background-color" , 'dodgerblue' );
          $("#senhaC").css("border-color" , 'dodgerblue');
        }
      });
      $("#cadastro").submit(function(){
        var senha = $("#senha").val();
        if( senha === ""){
          $("#senha").parent().find("label").css("background-color" , 'rgba(256,000,000)');
          $("#senha").css("border-color" , 'rgba(256,000,000)');
          $("#senha").focus();
          return false;
        }else{
          $("#senha").parent().find("label").css("background-color" , 'dodgerblue' );
          $("#senha").css("border-color" , 'dodgerblue');
        }
      });
      $("#cadastro").submit(function(){
        var email = $("#email").val();
        if( email === ""){
          $("#email").parent().find("label").css("background-color" , 'rgba(256,000,000)');
          $("#email").css("border-color" , 'rgba(256,000,000)');
          $("#email").focus();
          return false;
        }else{
          $("#email").parent().find("label").css("background-color" , 'dodgerblue' );
          $("#email").css("border-color" , 'dodgerblue');
        }
      });
      $("#cadastro").submit(function(){
        var user = $("#user").val();
        if( user === ""){
          $("#user").parent().find("label").css("background-color" , 'rgba(256,000,000)');
          $("#user").css("border-color" , 'rgba(256,000,000)');
          $("#user").focus();
          return false;
        }else{
          $("#user").parent().find("label").css("background-color" , 'dodgerblue' );
          $("#user").css("border-color" , 'dodgerblue');
        }
      });
      $("#cadastro").submit(function(){
        var user = $("#user").val();
        var email = $("#email").val();
        var senha = $("#senha").val();
        var senhaC = $("#senhaC").val();
        if(senhaC !== senha && senhaC !==""){
          $(".aviso-form-inicial").show();
          $(".aviso-form-inicial").find("p").text("confirmar senha não esta igual a senha, tente de novo");
          return false;
        }else{
          if( user === ""){
            $(".aviso-form-inicial").show();
            $(".aviso-form-inicial").find("p").text("você precisa preencher o nome de usuário")
          }else if( email === ""){
            $(".aviso-form-inicial").show();
            $(".aviso-form-inicial").find("p").text("você precisa digitar um E-mail")
          }else if( senha === ""){
            $(".aviso-form-inicial").show();
            $(".aviso-form-inicial").find("p").text("você precisa digitar um senha")
          }else{
            $(".aviso-form-inicial").show();
            $(".aviso-form-inicial").find("p").text("você precisa comfirmar a senha")
          }
        }
      })
    })