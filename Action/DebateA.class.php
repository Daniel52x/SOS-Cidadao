<?php // PAREI AQUI DIA 03/08/18
namespace Action;
use Model\DebateM;
use Core\DebateDenuncia;
use Core\Usuario;
use Core\Mensagens;
use Classes\TratarImg;
use Classes\Paginacao;
use Classes\TratarDataHora;
class DebateA extends DebateM{
    
    private $sqlInsert = "INSERT INTO debate(img_deba, nome_deba, dataHora_deba, tema_deba, descri_deba, cod_usu) 
                VALUES ('%s','%s','%s','%s','%s','%s')";

    private $sqlInserirPartici = "INSERT INTO debate_participante(cod_deba, cod_usu, data_inicio_lista, data_fim_lista, ind_visu_criador)
                                        VALUES ('%s','%s','%s',null,'%s')";

    private $sqlSelect = "SELECT cod_deba, img_deba, nome_deba, dataHora_deba, tema_deba, descri_deba, 
                                        debate.cod_usu,nome_usu, img_perfil_usu
                                FROM debate INNER JOIN usuario ON (usuario.cod_usu = debate.cod_usu) 
                                INNER JOIN tipo_usuario ON (usuario.cod_tipo_usu = tipo_usuario.cod_tipo_usu) 
                                WHERE descri_tipo_usu = 'Comum' AND %s  %s %s";

    private $sqlSelectQuantDeba = "SELECT COUNT(*) FROM debate INNER JOIN usuario ON (usuario.cod_usu = debate.cod_usu) 
                                        INNER JOIN tipo_usuario ON (usuario.cod_tipo_usu = tipo_usuario.cod_tipo_usu)      
                                        WHERE status_deba = 'A' AND descri_tipo_usu = 'Comum'  AND status_usu = 'A' %s";

    private $sqlSelectQuantPart = "SELECT COUNT(*) FROM debate_participante INNER JOIN usuario ON (usuario.cod_usu = debate_participante.cod_usu) 
                                        INNER JOIN tipo_usuario ON (usuario.cod_tipo_usu = tipo_usuario.cod_tipo_usu)      
                                        WHERE status_lista = 'A' AND descri_tipo_usu = 'Comum' AND data_fim_lista is null AND status_usu = 'A' AND cod_deba = '%s'";

    private $sqlVerificarSeEstaParticipando = "SELECT COUNT(*) FROM debate_participante 
                                                    INNER JOIN usuario ON (usuario.cod_usu = debate_participante.cod_usu)
                                                    INNER JOIN debate ON (debate_participante.cod_deba = debate.cod_deba)    
                                                    WHERE data_fim_lista is null AND debate_participante.cod_deba = '%s' 
                                                    AND debate_participante.cod_usu = '%s' AND status_lista = '%s'";
    
    private $sqlListarParticipantes = "SELECT %s FROM debate_participante
                                                    INNER JOIN usuario ON (debate_participante.cod_usu = usuario.cod_usu)
                                                    INNER JOIN debate ON (debate_participante.cod_deba = debate.cod_deba)
                                                    WHERE status_lista = 'A' AND
                                                    data_fim_lista is null AND %s AND debate.cod_deba = '%s'";

    private $sqlListarDebatesQParticipa = "SELECT img_deba, nome_deba, debate.cod_deba,
                                                (SELECT COUNT(*) FROM mensagem INNER JOIN mensagem_visualizacao
                                                    ON(mensagem_visualizacao.cod_mensa = mensagem.cod_mensa)
                                                    WHERE mensagem.cod_deba = debate.cod_deba AND mensagem_visualizacao.cod_usu = '%s'
                                                    AND status_visu = 'I') as quantidade,
                                                    (SELECT dataHora_mensa_visu FROM mensagem_visualizacao INNER JOIN mensagem 
                                                    ON(mensagem_visualizacao.cod_mensa = mensagem.cod_mensa) 
                                                    WHERE mensagem.cod_deba = debate.cod_deba AND mensagem_visualizacao.cod_usu = '%s' 
                                                    ORDER BY dataHora_mensa_visu DESC LIMIT 1) as HoraEnvio                                                    
                                                      FROM debate 
                                                INNER JOIN debate_participante ON (debate_participante.cod_deba = debate.cod_deba)
                                                INNER JOIN usuario ON (debate_participante.cod_usu = usuario.cod_usu)
                                                WHERE status_lista = 'A' AND 
                                                data_fim_lista is null AND %s AND debate_participante.cod_usu = '%s'
                                                ORDER BY HoraEnvio DESC ";
    
    private $sqlListDebaQuandoAberto = "SELECT img_deba, nome_deba, debate.cod_deba, debate.cod_usu,dataHora_deba, nome_usu 
                                            FROM debate INNER JOIN usuario ON (usuario.cod_usu = debate.cod_usu) 
                                            INNER JOIN tipo_usuario ON (usuario.cod_tipo_usu = tipo_usuario.cod_tipo_usu) 
                                            WHERE descri_tipo_usu = 'Comum' AND %s  %s %s";


    private $whereListFromALL = "status_usu = 'A' AND status_deba = 'A' ";
    private $sqlSelectDonoDeba = "SELECT cod_usu FROM debate WHERE cod_deba = '%s'";

    private $whereIdDeba = "AND cod_deba = '%s'";

    private $whereIdUser = " AND debate.cod_usu = '%s' ";
   
    private $sqlPaginaAtual;

    private $sqlUpdateStatusDeba = "UPDATE debate SET status_deba = '%s' WHERE cod_deba = '%s' AND cod_usu = '%s'";

    private $sqlUpdateParticipante = "UPDATE debate_participante SET status_lista = '%s' WHERE cod_deba = '%s' AND cod_usu = '%s'";

    private $sqlUpdateDebate = "UPDATE debate SET img_deba = '%s', nome_deba = '%s', tema_deba  = '%s', descri_deba = '%s' WHERE cod_deba = '%s' AND %s";

    public function tratarImagem(){ // Mexer depois nessa funcao
        //Fazer a parada da thumb       
        $tratar = new TratarImg();
        $novoNome = $tratar->tratarImagem($this->getImgDeba(), 'debate');
        $this->setImgDeba($novoNome);
        return;
    } 

    public function insert(){
        $usuario = new Usuario();
        $usuario->setCodUsu($this->getCodUsu());
        $tipo = $usuario->getDescTipo(); 

        $this->tratarImagem();

        $DataHora = new \DateTime('NOW');
        $DataHoraFormatadaAmerica = $DataHora->format('Y-m-d H:i:s'); 

        $sql = sprintf($this->sqlInsert,
                        $this->getImgDeba(),
                        $this->getNomeDeba(),
                        $DataHoraFormatadaAmerica,
                        $this->getTemaDeba(),
                        $this->getDescriDeba(),
                        $this->getCodUsu()
        );

        $inserir = $this->runQuery($sql);
        $idInserido = $this->last();
        $this->setCodDeba($idInserido);
        $this->inserirParticipante('I',TRUE);//Aqui vai inserir o dono
        if(!$inserir->rowCount()){  // Se der erro cai nesse if          
            throw new \Exception("Não foi possível realizar o cadastro da publicacao",13);   
        }

        $res = $usuario->getDadosUser();
        $nome = $res[0]['nome_usu'];
        $this->inserirMensagemSistema($nome . " criou o debate");

        
        return;        
    }

    public function ListFromALL($pagina = null){ // Listar todas as debates
       
        $sqlPaginacao = $this->controlarPaginacao($pagina, null);
        $sql = sprintf($this->sqlSelect,
                        $this->whereListFromALL,                       
                       ' AND 1=1', //colocar um AND 1=1 pq nao tem mais nada, se nao colocar da pau
                       $sqlPaginacao                       
                       
        );        
        $res = $this->runSelect($sql);
        
        $dadosTratados = $this->tratarDados($res);
        //var_dump($dadosTratados);
        return $dadosTratados;
    }

    public function listByIdDeba($atributo = 'sqlSelect',$restricao = null){ // Listar pelo id da publicacao     
        if(!empty($this->getCodUsu())){
            $usuario = new Usuario();
            $usuario->setCodUsu($this->getCodUsu());
            $tipoUsu = $usuario->getDescTipo();
        }     

        $prepararWherePubli = sprintf($this->whereIdDeba, $this->getCodDeba());         
        $sql = sprintf($this->{$atributo},
                        $this->whereListFromALL,
                        $prepararWherePubli,
                        ' %s '
        );  

        if($restricao == null OR ($tipoUsu == 'Adm' OR $tipoUsu == 'Moderador')){ // Nao precisa ser o dono da publicacao
            $sql = sprintf(
                $sql,
                ' AND 1=1'
            );
            $erro = "Não foi possível fazer o select"; // Se der erro aparece esta mensagem            
        }else{ // precisa ser o dono
            $sql = sprintf(
                $sql,
                sprintf(
                    ' AND debate.cod_usu = ' . $this->getCodUsu()
                )
            );
            $erro = "Você nao tem permissao para esta publicacao";// Se der erro aparece esta mensagem
        }
        $res = $this->runSelect($sql);
        if(empty($res)){
            throw new \Exception($erro,9); 
        }        
        $dadosTratados = $this->tratarDados($res); 
        //var_dump($dadosTratados);
        return $dadosTratados;
        
    }

    public function ListByIdUser($pagina = null, $idUserVisuali = null){        
        $prepararWhereUsu = sprintf($this->whereIdUser, $this->getCodUsu()); 
        $sqlPaginacao = $this->controlarPaginacao($pagina, $prepararWhereUsu );  
        $sql = sprintf($this->sqlSelect,
                        $this->whereListFromALL,                       
                        $prepararWhereUsu, //colocar um AND 1=1 pq nao tem mais nada, se nao colocar da pau
                        $sqlPaginacao                       
                       
        );  
        $res = $this->runSelect($sql);
        if(empty($res)){
            //throw new \Exception("Não foi possível fazer o select",9); 
            return;
        }  
        if($idUserVisuali != null){ // Mudar o id usuario para o id do usuario q esta vendo o perfil
            $this->setCodUsu($idUserVisuali);
        }
        $dadosTratados = $this->tratarDados($res);
        //var_dump($dadosTratados);
        return $dadosTratados;

    }
    public function tratarDados($dados, $indDebateMensa = null){
        $contador = 0;
               
        while($contador < count($dados)){//Nesse while so entra a parte q me interresa
            if($indDebateMensa == null){
                $dados[$contador]['dataHora_deba'] = $this->tratarHora($dados[$contador]['dataHora_deba']);//Calcular o tempo
                $dados[$contador]['qtdParticipantes'] = $this->quantidadeTotalParticipantes($dados[$contador]['cod_deba']);//Calcular o total de participantes
            
            
                if(!empty($this->getCodUsu())){//Só entar aqui se ele estiver logado
                    $dados[$contador]['indParticipa'] =  $this->verificarSeParticipaOuNao($dados[$contador]['cod_deba']);//Verificar se ele participa do debate
                    $dados[$contador]['indDenunComen'] =  $this->getVerificarSeDenunciou($dados[$contador]['cod_deba']);//Verificar se ele denunciou o debate
                    //Me retorna um bollenao
                }
            }else{
                $dados[$contador]['dataHora_deba'] = $this->tratarHora($dados[$contador]['HoraEnvio'], TRUE);//Calcular o tempo
            }
            
            $contador++;
        }  
        
        return $dados;
    }

    public function tratarHora($hora, $indDebateMensa = null){ 
        $tratarHoras = new TratarDataHora($hora);
        if($indDebateMensa == null){
            return $tratarHoras->calcularTempo('debate','N');
        }else{
            return $tratarHoras->tratarHoraDebateMensagem();
        }
        
    }

    public function quantidadeTotalParticipantes($cod){ // Pegar quantidade total de participantes
        
        $sql = sprintf($this->sqlSelectQuantPart,                             
                        $cod
                    );        
          
        $res = $this->runSelect($sql);
        return $res[0]['COUNT(*)'];
    }

    public function verificarSeParticipaOuNao($codDeba, $indErro = null, $status = 'A'){ //Verifica se o usuario ja esta participando
        $sql = sprintf($this->sqlVerificarSeEstaParticipando,
                        $codDeba,
                        $this->getCodUsu(),
                        $status);          
        $res = $this->runSelect($sql);
        $quantidade = $res[0]['COUNT(*)'];
        if($quantidade > 0){ //Se for maior q zero é pq ele curtiu            
            return TRUE; // participa
        }

        if($indErro != null){ // em alguns casos preciso de um erro            
            $usuario = new Usuario();
            $usuario->setCodUsu($this->getCodUsu());
            $tipo = $usuario->getDescTipo();
            if($tipo == 'Adm' OR $tipo == 'Moderador' OR $tipo == 'Prefeitura' OR $tipo == 'Funcionario'){ // se por um acaso for adm pode entrar tranquilamente               
                return TRUE;
            }else{
                throw new \Exception("Não participa do debate",9);
            }           
        }

        $sql = sprintf($this->sqlVerificarSeEstaParticipando,
                        $codDeba,
                        $this->getCodUsu(),
                        'B');          
        $res = $this->runSelect($sql);
        $quantidade = $res[0]['COUNT(*)'];
        if($quantidade > 0){ //Se for maior q zero é pq ele foi banido            
            return 'Banido'; // banido
        }
        return FALSE;
    }

    public function listarParticipantes($campos){ // listar participantes do debate
        $sql = sprintf(
            $this->sqlListarParticipantes,
            $campos,
            $this->whereListFromALL,
            $this->getCodDeba()
        );
        
        $resultado = $this->runSelect($sql);          
        return $resultado;
    }   

    public function listarDebatesQpartcipo(){
        $sql = sprintf(
            $this->sqlListarDebatesQParticipa,
            $this->getCodUsu(),
            $this->getCodUsu(),
            $this->whereListFromALL,
            $this->getCodUsu()
        );
        $resultado = $this->runSelect($sql); 
        $resultado2 = $this->tratarDados($resultado, TRUE);  
        return $resultado2;
    }

    public function entrarDebate(){ // entrar no debate        

        $verifi = $this->verificarSeParticipaOuNao($this->getCodDeba());        
        if($verifi){ // se ja participar estoura um erro
            throw new \Exception("Você ja participa", 9);
        }else if($verifi == 'Banido'){
            throw new \Exception("Você não pode participar pois foi banido", 9);
        }

        $qtdParti = $this->quantidadeTotalParticipantes($this->getCodDeba());
        if($qtdParti > 15){ // se tiver muitos participantes
            throw new \Exception("Limite de participantes alcançado", 15);
        }

        $this->inserirParticipante('N');        
    }

    public function sairDebate($indRemoverUser = null,$codUsuApagar = null){
        $usuario = new Usuario();
        $usuario->setCodUsu($this->getCodUsu());
        $tipo = $usuario->getDescTipo();   
        if($tipo == 'Adm' or $tipo == 'Moderador'){ // Administracao apagando
            $sql = sprintf(
                $this->sqlSelectDonoDeba,
                $this->getCodDeba()            
            );
            $res = $this->runSelect($sql);              
            if($res[0]['cod_usu'] == $codUsuApagar){
                throw new \Exception("Não é possivel remover o dono do debate");
            }

            $this->updateStatusParti('B', $codUsuApagar,TRUE, 'Administracao');
            return 1; // adm
        }

        $res = $this->listByIdDeba('sqlListDebaQuandoAberto');

        if($res[0]['cod_usu'] == $this->getCodUsu()){
            if($indRemoverUser != null){ // dono do debate esta apagando usuario
                $this->updateStatusParti('B', $codUsuApagar);
                return 2; // dono que removeu
            }
            $this->updateStatusDeba('I'); // apagar debate, se o dono sair o debate é "apagado"
            return 3;
        }else{
            $this->updateStatusParti('I'); // NAO É O DONO
            return 4; // usuario que esta saindo
        }
    }

    public function quantidadeTotalPubli($where){//Pegar a quantidade de debates
        if($where != null){ // Se for passado o paramentro null, nao tem restriçoes retorna todas as debates
            $sql = sprintf($this->sqlSelectQuantDeba,
                                $where); 
        }else{
            $sql = sprintf($this->sqlSelectQuantDeba,
                                'AND 1=1');
        }
          
        $res = $this->runSelect($sql);
        return $res[0]['COUNT(*)'];
    }

    public function getVerificarSeDenunciou($idDeba = false){       
        if(!$idDeba){
            $idDeba = $this->getCodDeba();
        }
        $idUser = $this->getCodUsu();

        $denun = new DebateDenuncia();
        $denun->setCodDeba($idDeba);
        $denun->setCodUsu($idUser);
        return $denun->verificarSeDenunciou();
    }

    public function controlarPaginacao($pagina = null, $where){ // Fazer o controle da paginacao       
        $paginacao = new Paginacao(); 
        $paginacao->setQtdPubliPaginas(6); // Setar a quantidade de publicacoes por pagina
        
        $quantidadeTotalPubli = $this->quantidadeTotalPubli($where);   //Pega a quantidade de publicacoes no total          
        
        $sqlPaginacao = $paginacao->prapararSql('dataHora_deba','desc', $pagina, $quantidadeTotalPubli);//Prepare o sql
        $this->setQuantidadePaginas($paginacao->getQuantidadePaginas());//Seta a quantidade de paginas no total
        $this->setPaginaAtual($paginacao->getPaginaAtual());
        return $sqlPaginacao;
        
    }

    public function inserirParticipante($status, $indDono = null){
        $usuario = new Usuario();
        $usuario->setCodUsu($this->getCodUsu());
        $tipo = $usuario->getDescTipo();       
       

        if($tipo != 'Comum'){
            throw new \Exception("Você nao tem permissao",9);
        }
        $ind = $this->verificarSeParticipaOuNao($this->getCodDeba(),null,'I');
        if($ind > 0 ){ // mudar apenas o status e nao inserir outra linha
            $this->updateStatusParti('A');            
            return;
        }

        $DataHora = new \DateTime('NOW');
        $DataHoraFormatadaAmerica = $DataHora->format('Y-m-d H:i:s'); 

        $sql = sprintf($this->sqlInserirPartici,
                        $this->getCodDeba(),
                        $this->getCodUsu(),
                        $DataHoraFormatadaAmerica,
                        $status
        );
        $inserir = $this->runQuery($sql);        
        if(!$inserir->rowCount()){  // Se der erro cai nesse if          
            throw new \Exception("Não foi possível realizar o cadastro da publicacao",13);   
        }       
        if($indDono == null){ // inserir a mensagem caso ele nao for o dono
            $res = $usuario->getDadosUser();
            $nome = $res[0]['nome_usu'];
            $this->inserirMensagemSistema($nome . " entrou no debate");
        }
        return;        
    }

    public function inserirMensagemSistema($texto){ // inserir noticias como mensagens
        $usuario = new Usuario();        
        $tirar = array('in',')','(',"'");// tirar a parte q eu nao quero
        $codUsuSistema = str_replace($tirar,"",$usuario->getCodUsuByTipoUsu("in('Sistema')")); // pegar id do usuario sistema
        $mensagem = new Mensagens($this->getCodDeba());
        $mensagem->setCodUsu($codUsuSistema);
        $mensagem->setCodDeba($this->getCodDeba());
        $mensagem->setTextoMensa($texto);
        $mensagem->inserirMensagem(True);
    }

    public function verificarDadosIguais($NovosDados, $dadosOriginais){
        $indIgual = 0;
        foreach($NovosDados as $chave => $valor){
            if($valor == $dadosOriginais[0][$chave]){                                 
                $indIgual++;
            }else{
                $chave;
            }
        }
        if($indIgual == count($NovosDados)){
            return true;
        }
        return false;
    }

    public function updateStatusDeba($status){        
        $usuario = new Usuario();
        $usuario->setCodUsu($this->getCodUsu());
        $tipo = $usuario->getDescTipo();       

        if($tipo == 'Adm' or $tipo == 'Moderador'){
            $sqlUpdateDeba = "UPDATE debate SET status_deba= '%s' WHERE cod_deba = '%s'";
            $sql = sprintf(
                $sqlUpdateDeba,
                $status,
                $this->getCodDeba()                
            );
        }else{
            $sql = sprintf(
                $this->sqlUpdateStatusDeba,
                $status,
                $this->getCodDeba(),
                $this->getCodUsu()
            );
        }    
        $resposta = $this->runQuery($sql);
        if(!$resposta->rowCount()){
            throw new \Exception("Não foi possível mudar o status",9);
        }

        return;
    }    
    
    public function updateStatusParti($status,$codUsuApagar = null, $indRemocaoForcado = null, $tipoUsu = null){ // mudar status do participante  
       
        if($codUsuApagar != null){ // dono esta eliminado um usuario, ou administracao
            if($tipoUsu == 'Administracao'){
                $mensagem = "A administração removeu ";
            }else{
                $mensagem = "O dono do debate removeu ";
            }
            $codUsu = $codUsuApagar; // codigo do usuario q tem q apagar
            $ind = 1;
        }else{
            $codUsu = $this->getCodUsu(); // codigo do usuario q tem q apagar, o próprio usuario esta saindo
            $ind = 2;
        }    

        $sql = sprintf(
            $this->sqlUpdateParticipante,
            $status,
            $this->getCodDeba(),
            $codUsu
        );
        $resposta = $this->runQuery($sql);
        if(!$resposta->rowCount()){
            throw new \Exception("Não foi possível mudar o status",9);
        }
        $usuario = new Usuario();
        $usuario->setCodUsu($codUsu);
        $res = $usuario->getDadosUser();
        $nome = $res[0]['nome_usu'];
        if($ind == 2){ // notificar
            if($status == 'A'){
                $this->inserirMensagemSistema($nome . " entrou no debate");
            }else{
                $this->inserirMensagemSistema($nome . " saiu do debate");
            }            
        }else if($ind == 1){ // admintracao ou dono do debate q esta apagando
            $this->inserirMensagemSistema($mensagem . $nome);
        }
        return;
    }    

    public function updateDebate(){
        $dados = $this->listByIdDeba('sqlSelect', true); 
        $novosDados = array(
            "nome_deba" => $this->getNomeDeba(),
            "tema_deba" => $this->getTemaDeba(),
            "descri_deba" => trim($this->getDescriDeba()),                
        );
        $dadosImg = $this->getImgDeba();       
        if($dadosImg == 'banana'){ // banana é o nome padrao q vem
            $novosDados["img_deba"] = $dados[0]['img_deba'];
        }else{
            $novosDados["img_deba"] = 'diferente';
        }        
        if($this->verificarDadosIguais($novosDados, $dados)){ // Verificar se os dados sao igual             
            return $this->getCodDeba(); // se for igual nao precisa dar update            
        }
        
        $usuario = new Usuario();
        $usuario->setCodUsu($this->getCodUsu());
        $tipoUsu = $usuario->getDescTipo(); 
        if($dadosImg != 'banana'){ // se ele mudar a imagem           
            $this->tratarImagem();
        }else{ // se nao mudar             
            $this->setImgDeba($dados[0]['img_deba']);
        }      
        
        $sql = sprintf(
            $this->sqlUpdateDebate,
            $this->getImgDeba(),
            $this->getNomeDeba(),
            $this->getTemaDeba(),
            $this->getDescriDeba(),
            $this->getCodDeba(),
            ' %s '
        );

        if($tipoUsu == 'Adm' or $tipoUsu == 'Moderador'){  // Se for adn ou moderador pode editar tranquilamente
            $sql = sprintf($sql, ' 1=1 ');            
        }else{ // Se nao for, precisa ser o dono
            $sql = sprintf(
                $sql,                           
                sprintf(
                    " cod_usu = '%s' ", // Adiciona um campo
                    $this->getCodUsu()
                )                
            );
        }
        
        $resposta = $this->runQuery($sql);    
        if($resposta->rowCount() <= 0 ){
                throw new \Exception("Não foi possivel editar o debate",13);
        }    
        return $this->getCodDeba();    
    }
}
