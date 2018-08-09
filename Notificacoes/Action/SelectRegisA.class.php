<?php 

namespace Notificacoes\Action;
use Notificacoes\Model\GenericaM;

class SelectRegisA extends GenericaM{ 

    private $sqlSelect = "SELECT cod_publi FROM publicacao WHERE cod_usu = '%s' AND status_publi = 'A'";

    private $sqlSelectComen = "SELECT cod_comen FROM comentario WHERE cod_usu = '%s' AND status_comen = 'A'";

    private $sqlSelectSalvos = "SELECT cod_publi from publicacao_salva where cod_usu = '%s' AND status_publi_sal = 'A' AND ind_visu_respos_prefei = 'N'";

    public function selectPubli(){
        $sql = sprintf( $this->sqlSelect,
                        $this->getCodUsu()
                    );
        $res = $this->runSelect($sql);
        //var_dump($res);
        $res2 = $this->gerarIn($res);       
        return $res2; // Retorna uma array Bidimensional
    }

    public function selectComen(){
        
        $sql = sprintf( $this->sqlSelectComen,
                        $this->getCodUsu()
                    );
        $res = $this->runSelect($sql);        
        $res2 = $this->gerarIn($res);       
        return $res2; // Retorna uma array Bidimensional
    }
    public function selectSalvos(){
        
        $sql = sprintf( $this->sqlSelectSalvos,
                        $this->getCodUsu()
                    );
        $res = $this->runSelect($sql);        
        $res2 = $this->gerarIn($res);       
        return $res2; // Retorna uma array Bidimensional
    }


    public function gerarIn($tipos = array()){// gerar o in, exemplo in('adm','moderador')
        $in = "in( ";
        $contador = 1;
        foreach ($tipos as $valores){
            foreach($valores as $valor){
                if($contador == count($tipos)){
                    $in.= "'$valor'" . ' )';
                }else{
                    $in.= "'$valor'".', ';
                }
            }            
            $contador++;            
        }
        return $in;
    }
}