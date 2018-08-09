describe comen_curtida; 
describe `mensagem_visualizacao`;
describe `publicacao_curtida`;

alter table comen_curtida add dataHora_comen_curti datetime not null;
alter table mensagem_visualizacao add dataHora_mensa_visu datetime not null;
alter table publicacao_curtida add dataHora_publi_curti datetime not null;

SELECT * FROM publicacao_curtida;

UPDATE comen_curtida set dataHora_comen_curti = now() WHERE cod_usu = 1 AND cod_comen = 3