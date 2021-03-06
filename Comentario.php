<?php
require_once('Config/Config.php');
require_once(SITE_ROOT.DS.'autoload.php');
use Core\Comentario;
use Core\Usuario;
use Classes\ValidarCampos;//dsadas///
session_start();
try{            
    $tipoUsuPermi = array('Comum','Prefeitura','Funcionario');
    Usuario::verificarLogin(1,$tipoUsuPermi);  // Tem q estar logado 

    $nomesCampos = array('texto', 'id');// Nomes dos campos que receberei do formulario
    $validar = new ValidarCampos($nomesCampos, $_POST);//Verificar se eles existem, se nao existir estoura um erro
    $validar->verificarTipoInt(array('id'), $_POST); 

    $texto = $_POST['texto'];
    $idPubli = $_POST['id'];
    $comentario = new Comentario();
    $comentario->setTextoComen($texto);
    $comentario->setCodUsu($_SESSION['id_user']);
    $comentario->setCodPubli($idPubli);
    $comentario->inserirComen();
    echo $_SESSION['tipo_usu'].".".$comentario->last().",".$comentario->quantidadeTotalPubli();    
    //echo "<script> javascript:window.location='view/reclamacao.php?ID=".$idPubli."';</script>";

}catch (Exception $exc){
    $erro = $exc->getCode();   
    $mensagem = $exc->getMessage();
    switch($erro){
        case 2://Não está logado  
            echo 'NLogado';
            //echo "<script> alert('$mensagem');javascript:window.location='./view/login.php';</script>";
            break;  
        case 11:// Erro no comentario
        case 12://Mexeu no insprnsionar elemento ou nao submeteu o formulario      
            echo "<script> alert('$mensagem');javascript:window.location='./view/todasreclamacoes.php';</script>";
            break;             
        default: //Qualquer outro erro cai aqui
            echo "<script> alert('$mensagem');javascript:window.location='./view/todasreclamacoes.php';</script>";
    }    
    
}


