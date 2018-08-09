<?php 

namespace Notificacoes\Action;
use Notificacoes\Model\GenericaM;

class SelectPubliCurtiA extends GenericaM{  
    private $sqlSelect = "SELECT usuario.nome_usu, titulo_publi, publicacao_curtida.cod_publi, dataHora_publi_curti 
                            FROM publicacao_curtida INNER JOIN usuario ON(publicacao_curtida.cod_usu = usuario.cod_usu)
                            INNER JOIN publicacao ON (publicacao_curtida.cod_publi = publicacao.cod_publi) 
                            WHERE ind_visu_dono_publi = 'N' or 'B'  AND status_publi_curti = 'A' AND publicacao_curtida.cod_publi %s 
                            AND publicacao.status_publi = 'A' ORDER BY publicacao_curtida.cod_publi, dataHora_publi_curti DESC, ind_visu_dono_publi DESC ";

    public function select(){ // Ja ta no esquema
        $ids = $this->getCodPubli();   
        //echo "<strong>IDS PUBLICACOES DO USUARIO: <br><br><br></strong>";        
        $sql = sprintf( $this->sqlSelect, $ids);
        $resultadoTemp = $this->runSelect($sql);        
        $tirar = array('in(',"'",')'," "); // tirar as partes da string q nao quero
        $ids2 = str_replace ($tirar,"",$ids);
        $idsNum = explode(',',$ids2); //Depois quebra em array
        $dadosArrumados = array(); 
        $dadosArrumados2 = array();        
        $contador = 0;
        $contador2 = 0;
        foreach($idsNum as $valor){
            while($contador < count($resultadoTemp)){
                if($resultadoTemp[$contador]['cod_publi'] == $valor){
                    $dadosArrumados[$contador2][] = $resultadoTemp[$contador];
                }
                $contador++;                
            }
            $contador = 0;
            $contador2++;
        }          
        $contador = 0;
        foreach($dadosArrumados as $valor){ // Arrumar os indices
            $dadosArrumados2[$contador] = $valor;
            $contador++;
        }  
        return $dadosArrumados2;
    }                            
}