<?php
require_once '../model/ContribuicaoLogCollection.php';
interface ApiCarneServiceInterface{
    public function gerarCarne(ContribuicaoLogCollection $contribuicaoLog);
    public function guardarSegundaVia($arquivos);//Considerar transformar em utilitário
}