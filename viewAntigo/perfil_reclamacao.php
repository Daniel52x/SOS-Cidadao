<?php
session_start();
    require_once('../Config/Config.php');
    require_once(SITE_ROOT.DS.'autoload.php');   
    use Core\Usuario;
    use Core\Publicacao;
    use Classes\ValidarCampos;
    
    try{   
        //$_SESSION['indNovaConta']  = true;
        if(isset($_SESSION['indNovaConta'])){ // se por um acaso for usuario novo
            unset($_SESSION['indNovaConta']);           
        }
        $usuPerfil = new Usuario();
        if(isset($_SESSION['id_user'])){ // se estiver logado   
            $usu = new Usuario();  
            $usu->setCodUsu($_SESSION['id_user']);         
            $resultado = $usu->getDadosUser(false,true);

            $tipoUsu = $_SESSION['tipo_usu'];
            if(isset($_GET['ID'])){ // quando for ver perfil de outras pessoas
                $validar = new ValidarCampos(array('ID'), $_GET);
                $validar->verificarTipoInt(array('ID'), $_GET); // Verificar se o parametro da url é um numero
                $id = $_GET['ID'];
                $usuPerfil->setCodUsu($_GET['ID']); 
                $dadosPerfil =  $usuPerfil->getDadosUser(false,true);     
            }else{ // seu propio perfil
                $id = $_SESSION['id_user'];                
                $dadosPerfil = $resultado;                            
            }      
        }else{ // Nao esta logado
            $validar = new ValidarCampos(array('ID'), $_GET);
            $validar->verificarTipoInt(array('ID'), $_GET); // Verificar se o parametro da url é um numero
            $id = $_GET['ID'];   
            $usuPerfil->setCodUsu($_GET['ID']);    
            $dadosPerfil =  $usuPerfil->getDadosUser(false,true);     
        }        
        $descPerfilVisu = $dadosPerfil[0]['descri_tipo_usu'];
        if($descPerfilVisu != 'Comum' AND $descPerfilVisu != 'Prefeitura'){ // Vendo perfil restrito
            if(!isset($_SESSION['id_user'])){ // Não esta logado
                throw new \Exception("Você nao tem permissao para este perfil12",1);
            }

            if($_SESSION['id_user'] != $dadosPerfil[0]['cod_usu']){// Logado, e nao esta no seu perfil
                switch($tipoUsu){
                    case 'Comum':
                    case 'Funcionario':
                        throw new \Exception("Você nao tem permissao para este perfil13",1);
                        break;
                    case 'Prefeitura':
                        if($descPerfilVisu != 'Funcionario'){
                            throw new \Exception("Você nao tem permissao para este perfil14",1);
                        }
                        break; 
                }
        }}    

        $publi = new Publicacao();    
        $publi->setCodUsu($id);
        isset($_GET['pagina']) ?: $_GET['pagina'] = null; 

        if($descPerfilVisu == 'Prefeitura'){
           $nomeLink1 = 'Reclamações Não Respondidas';
           $nomeLink2 = 'Reclamações Respondidas'; 
           $resposta = $publi->getPubliNRespo($_GET['pagina'], TRUE);  
        }else{
            isset($_SESSION['id_user']) ? $idVisualizador = $_SESSION['id_user'] : $idVisualizador = null;
            $nomeLink1 = 'Reclamação';
            $nomeLink2 = 'Debate';
            $resposta = $publi->ListByIdUser($_GET['pagina'], $idVisualizador);  
        }       
        
        
        //var_dump($_SESSION['id_user']);
        
           
        //var_dump($resposta);   
        $quantidadePaginas = $publi->getQuantidadePaginas();
        $pagina = $publi->getPaginaAtual();        

        
?>
<!DOCTYPE html>
<html lang=pt-br>
    <head>
        <title>S.O.S Cidadão</title>

        <meta charset=UTF-8> <!-- ISO-8859-1 -->
        <meta name=viewport content="width=device-width, initial-scale=1.0">
        <meta name=description content="Site de reclamações para a cidade de Barueri">
        <meta name=keywords content="Reclamação, Barueri"> <!-- Opcional -->
        <meta name=author content='equipe 4 INI3A'>

        <!-- favicon, arquivo de imagem podendo ser 8x8 - 16x16 - 32x32px com extensão .ico -->
        <link rel="shortcut icon" href="imagens/favicon.ico" type="image/x-icon">

        <!-- CSS PADRÃO -->
        <link href="css/default.css" rel=stylesheet>

        <!-- Telas Responsivas -->
        <link rel=stylesheet media="screen and (max-width:480px)" href="css/style480.css">
        <link rel=stylesheet media="screen and (min-width:481px) and (max-width:768px)" href="css/style768.css">
        <link rel=stylesheet media="screen and (min-width:769px) and (max-width:1024px)" href="css/style1024.css">
        <link rel=stylesheet media="screen and (min-width:1025px)" href="css/style1025.css">

        <!-- JS-->

        <script src="lib/_jquery/jquery.js"></script>
        <script src="js/js.js"></script>
        <script src="../teste.js"></script>

    </head>
    <body>
        <header>
            <img src="imagens/logo_oficial.png" alt="logo">
            <form action="pesquisa.php" method="get">
                <input type="text" name="pesquisa" id="pesquisa" placeholder="Pesquisar">
                <button type="submit"><i class="icone-pesquisa"></i></button>
            </form>
            <nav class="menu">
                <ul>
                    <li><nav class="notificacoes">
                        <h3>notificações<span id="not-fechado"></span></h3>
                        <ul id="menu23">
                            
                            <li>
                        </ul>
                    </nav><a href="#" id="abrir-not"><i class="icone-notificacao" id="noti"></i>Notificações</a></li>
                    <li><a href="todasreclamacoes.php"><i class="icone-reclamacao"></i>Reclamações</a></li>
                    <li><a href="todosdebates.php"><i class="icone-debate"></i>Debates</a></li>
                </ul>
            </nav>
            <?php
                if(!isset($resultado)){
                    echo '<a href="login.php"><i class="icone-user" id="abrir"></i></a>';
                }else{
                    echo '<i class="icone-user" id="abrir"></i>';
                }
            ?>
        </header>
        <?php
                if(isset($resultado) AND !empty($resultado)){
        ?>
        <div class="user-menu">
           
           <a href="javascript:void(0)" class="fechar">&times;</a>            
           <div class="mini-perfil">
               <div>    
                   <img src="../Img/perfil/<?php echo $resultado[0]['img_perfil_usu'] ?>" alt="perfil">
               </div>    
                   <img src="../Img/capa/<?php echo $resultado[0]['img_capa_usu'] ?>" alt="capa">
                   <p><?php echo $resultado[0]['nome_usu'] ?></p>
           </div>
          
           <nav>
               <ul>
                   <?php
                      require_once('opcoes.php');                        
                   ?>
               </ul>
           </nav>
           
       </div>       
       <?php
           }
       ?>


        <div id="container">
            <section class="perfil-base">
                <div class="perfil">
                        <?php 
                            if(isset($_SESSION['id_user']) AND $_SESSION['id_user'] == $dadosPerfil[0]['cod_usu']){
                        ?>
                            <i class="icone-edit-full" id="trocar-capa" title="Alterar a foto de capa"></i>
                            <!-- 
                            <form action="../UpdateImagem.php" method="post" enctype="multipart/form-data">
                                    <label for="imagem"><i class="icone-edit-full"></i></label>
                                    <input type="file" id="imagem">
                                    <input type="hidden" value="capa" name="tipo">                                
                            </form>
                            -->
                        <?php 
                            }
                        ?>
                    <img src="../Img/capa/<?php echo $dadosPerfil[0]['img_capa_usu'] ?>"> 
                   
                    <div class="perfil-info">
                        <p><?php echo $dadosPerfil[0]['nome_usu'] ?></p>
                        <div>
                            <img src="../Img/perfil/<?php echo $dadosPerfil[0]['img_perfil_usu'] ?>">
                        </div>
                        <?php 
                            if(isset($_SESSION['id_user']) AND $_SESSION['id_user'] == $dadosPerfil[0]['cod_usu']){
                        ?>
                            <i class="icone-edit-full" id="trocar-perfil" title="Alterar a foto de perfil"></i>
                            <!-- 
                            <form action="../UpdateImagem.php" method="post" enctype="multipart/form-data">
                                <label for="imagem"><i class="icone-edit-full" title="Alterar a foto de perfil"></i></label>
                                <input type="file" id="imagem">
                                <input type="hidden" value="perfil" name="tipo">  
                            </form>-->
                        <?php 
                            }
                        ?>
                       
                        
                    </div>
                </div>
               
            </section>
            <nav class="menu-perfil">
                <ul class="espacos">

                    <li class="ativo"><a href="#r"><?php echo $nomeLink1 ?></a></li>
                    <?php 
                        if(isset($_GET['ID'])){                    
                            echo '<li><a href="perfil_debate.php?ID='.$dadosPerfil[0]['cod_usu'].'">'.$nomeLink2.'</a></li>';
                        }else{
                            echo '<li><a href="perfil_debate.php">'.$nomeLink2.'</a></li>';
                        }
                    ?>                   
                    
                </ul>
            </nav>
            <section class="alinha-item">
            <?php
                if(empty($resposta)){
                    echo '<span style="color:white">Não há reclamações para serem exibidos</span>';
                    exit();
                }
                $contador = 0;
                while($contador < count($resposta)){                
            ?>  
                <div class="item-publicacao">
                    <div class="item-topo">
                        <a href="#">
                        <div>
                            <img src="../Img/perfil/<?php echo $resposta[$contador]['img_perfil_usu']?>">
                        </div>
                        <p><span class="negrito"><?php echo $resposta[$contador]['nome_usu']?></a></span><time><?php echo $resposta[$contador]['dataHora_publi']?></time></p>
                        <div class="mini-menu-item">
                            <i class="icone-3pontos"></i>
                            <div>
                                <ul>
                                    <?php
                                        if(isset($resposta[$contador]['indDenunPubli']) AND $resposta[$contador]['indDenunPubli'] == TRUE){ // Aparecer quando o user ja denunciou            
                                            echo '<li><i class="icone-bandeira"></i><span class="negrito">Denunciado</span></li>';        
                                        }else if(isset($_SESSION['id_user']) AND $_SESSION['id_user'] != $resposta[$contador]['cod_usu']){ // Aparecer apenas naspublicaçoes q nao é do usuario
                                            if($tipoUsu == 'Comum' or $tipoUsu == 'Prefeitura' or $tipoUsu == 'Funcionario'){
                                                //echo '<li><a href="../Templates/DenunciarPublicacaoTemplate.php?ID='.$resposta[$contador]['cod_publi'].'"><i class="icone-bandeira"></i>Denunciar</a></li>';                                                        
                                                echo '<li class="denunciar-item"><a href="#"><i class="icone-bandeira"></i>Denunciar</a></li>';

                                            }                    
                                        }else if(!isset($_SESSION['id_user'])){ // aparecer parar os usuario nao logado
                                                //echo '<li><a href="../Templates/DenunciarPublicacaoTemplate.php?ID='.$resposta[$contador]['cod_publi'].'"><i class="icone-bandeira"></i>Denunciar</a></li>';
                                                echo '<li class="denunciar-item"><a href="#"><i class="icone-bandeira"></i>Denunciar</a></li>';
                                        } 
                                    ?>
                                    <?php
                                            if(isset($_SESSION['id_user']) AND $_SESSION['id_user'] == $resposta[$contador]['cod_usu']){
                                                echo '<li><a href="../ApagarPublicacao.php?ID='.$resposta[$contador]['cod_publi'].'"><i class="icone-fechar"></i></i>Remover</a></li>';                                                
                                                echo '<li><a href="reclamacao-update.php?ID='.$resposta[$contador]['cod_publi'].'"><i class="icone-edit-full"></i></i>Alterar</a></li>';
                                            }else if(isset($tipoUsu) AND ($tipoUsu == 'Adm' or $tipoUsu == 'Moderador')){
                                                echo '<li><a href="../ApagarPublicacao.php?ID='.$resposta[$contador]['cod_publi'].'"><i class="icone-fechar"></i></i>Remover</a></li>';
                                                // Icone para apagar usuaario
                                                //echo '<a href="../ApagarUsuario.php?ID='.$resposta[0]['cod_usu'].'">Apagar Usuario</a>';
                                                echo '<li><a href="reclamacao-update.php?ID='.$resposta[$contador]['cod_publi'].'"><i class="icone-edit-full"></i></i>Alterar</a></li>';
                                            }
                                    ?> 
                                    
                                    <?php
                                            if(isset($_SESSION['id_user']) AND isset($resposta[$contador]['indSalvaPubli']) AND $resposta[$contador]['indSalvaPubli'] == TRUE){//Salvou
                                                echo '<li><a href="../SalvarPublicacao.php?ID='.$resposta[$contador]['cod_publi'].'"><i class="icone-salvar-full"></i>Salvo</a></li>';
                                            }else if(isset($_SESSION['id_user']) AND isset($resposta[$contador]['indSalvaPubli']) AND $resposta[$contador]['indSalvaPubli'] == FALSE){//Nao salvou
                                                echo '<li><a href="../SalvarPublicacao.php?ID='.$resposta[$contador]['cod_publi'].'"><i class="icone-salvar"></i>Salvar</a></li>';
                                            }else if(!isset($_SESSION['id_user'])){ // aparecer parar os usuario nao logado
                                                echo '<li><a href="../SalvarPublicacao.php?ID='.$resposta[$contador]['cod_publi'].'"><i class="icone-salvar"></i>Salvar</a></li>';
                                            } 
                                    ?> 

                                </ul>
                            </div>
                            <div class="modal-denunciar">
                                    <div class="modal-denunciar-fundo"></div>
                                    <div class="box-denunciar">
                                        <div>
                                            <h1>Qual o motivo da denuncia?</h1>
                                            <span class="fechar-denuncia">&times;</span>
                                        </div>
                                       
                                        <form form method="post" action="../DenunciarPublicacao.php">
                                            <textarea placeholder="Qual o motivo?" id="motivo" name="texto"></textarea>
                                            <input type="hidden" name="id_publi" value="<?php echo $resposta[$contador]['cod_publi'] ?>">                
                                            <button type="submit"> Denunciar</button>
                                        </form>
                                        
                                    </div>
                                </div>
                        </div>
                    </div>
                    <a href="reclamacao.php?ID=<?php echo $resposta[$contador]['cod_publi'] ?>">
                    <?php
                        if(!empty($resposta[$contador]['img_publi'])){
                    ?> 
                        <figure>
                            <img src="../Img/publicacao/<?php echo $resposta[$contador]['img_publi']?>">
                        </figure>   
                    <?php
                        }
                    ?>  
                        <p><?php echo $resposta[$contador]['titulo_publi']?></p>
                        </a>
                        <div class="item-baixo">   
                            <i class="icone-local"></i><p><?php echo $resposta[$contador]['endereco_organizado_fechado']?></p>
                            <div>    
                                <span><?php echo $resposta[$contador]['quantidade_curtidas']?></span><i class="icone-like"></i>
                                <span><?php echo $resposta[$contador]['quantidade_comen']?></span><i class="icone-comentario"></i>
                            </div>
                        </div>
                </div>
                <?php                  
                    $contador++;
                    }
                ?>                     
            </section>            
        </div>
        <ul>
        <?php
            if($quantidadePaginas != 1){
                $contador = 1;
                while($contador <= $quantidadePaginas){
                    if(isset($pagina) AND $pagina == $contador){
                        if(isset($_GET['ID'])){
                            echo '<li class="jaca"><a href="perfil_reclamacao.php?pagina='.$contador.'&ID='.$_GET['ID'].'">Pagina'.$contador.'</a></li>'  ; 
                        }else{
                            echo '<li class="jaca"><a href="perfil_reclamacao.php?pagina='.$contador.'">Pagina'.$contador.'</a></li>'  ; 
                        }                         
                    }else{
                        if(isset($_GET['ID'])){
                            echo '<li class="jaca"><a href="perfil_reclamacao.php?pagina='.$contador.'&ID='.$_GET['ID'].'">Pagina'.$contador.'</a></li>'  ; 
                        }else{
                            echo '<li class="jaca"><a href="perfil_reclamacao.php?pagina='.$contador.'">Pagina'.$contador.'</a></li>'  ; 
                        }                         
                    }                    
                    $contador++;        
                }
            }
            
        ?>
        </ul>
    </body>
</html>
<?php

}catch (Exception $exc){     
    $erro = $exc->getCode();   
    $mensagem = $exc->getMessage();
    switch($erro){
        case 2://Ja esta logado  
        case 6://Ja esta logado 
        case 1:
            echo "<script> alert('$mensagem');javascript:window.location='index.php';</script>";
            break;
        default:
            echo "<script> alert('$mensagem');javascript:window.location='index.php';</script>";
    }      
}finally{

}