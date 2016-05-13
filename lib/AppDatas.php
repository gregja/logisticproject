<?php

namespace Logistic ;

abstract class AppDatas {

    public static function OTByDateColumns () {
        $columns = array();
        $columns['po.nocd']=array('data_type'=>'NUMERIC', 'length'=>7, 'text'=>"Bon Livraison");
        $columns['po.nopo']=array('data_type'=>'NUMERIC', 'length'=>7, 'text'=>"Position");
        $columns['po.nocl']=array('data_type'=>'NUMERIC', 'length'=>7, 'text'=>"Destin.");
        $columns['po.noex']=array('data_type'=>'NUMERIC', 'length'=>7, 'text'=>"Expédit.");
//        $columns['po.noaden']=array('data_type'=>'NUMERIC', 'length'=>7, 'text'=>"n° adr. enl.");
//        $columns['po.covien']=array('data_type'=>'CHAR', 'length'=>5, 'text'=>"Cod.ville");
        $columns['po.noadlv']=array('data_type'=>'NUMERIC', 'length'=>7, 'text'=>"n° adr.");
        $columns['po.covilv']=array('data_type'=>'CHAR', 'length'=>5, 'text'=>"Cod.ville");
        $columns['po.dalvth']=array('data_type'=>'DATE', 'length'=>4, 'text'=>"Date Liv.");
        $columns['po.hhlvth']=array('data_type'=>'NUMERIC', 'length'=>4, 'text'=>"Heure Livr.");
//        $columns['po.daenth']=array('data_type'=>'DATE', 'length'=>4, 'text'=>"Date Enl. Théo.");
//        $columns['po.hhenth']=array('data_type'=>'NUMERIC', 'length'=>4, 'text'=>"Heure Enl. Théo.");
        $columns['po.qtlot']=array('data_type'=>'DECIMAL', 'length'=>5, 'text'=>"Qté lot");
//        $columns['posg.sgnoen']=array('data_type'=>'NUMERIC', 'length'=>7, 'text'=>"n° adr. enl.");
//        $columns['posg.sgvien']=array('data_type'=>'CHAR', 'length'=>5, 'text'=>"Code ville");
//        $columns['posg.sgnolv']=array('data_type'=>'NUMERIC', 'length'=>7, 'text'=>"n° adr. liv.");
//        $columns['posg.sgvilv']=array('data_type'=>'CHAR', 'length'=>5, 'text'=>"C.ville");
        $columns['posg.sgdade']=array('data_type'=>'DATE', 'length'=>4, 'text'=>"Date départ");
        $columns['posg.sgdaar']=array('data_type'=>'DATE', 'length'=>4, 'text'=>"Date arrivée");
        $columns['posg.nosgt']=array('data_type'=>'INTEGER', 'length'=>4, 'text'=>"ID Voyage");
        return $columns;
    }

    public static function OTByDateQuery () {
        $columns = join(', ', array_keys(self::OTByDateColumns()));
        return <<<BLOC_SQL
Select
{$columns}
, POSG.NOSGT as ID
From TESTFIC{SEPARATOR}PO PO
Join TESTFIC{SEPARATOR}POSG POSG
  On POSG.COAGPO = PO.COAG
  And POSG.NOPO = PO.NOPO
  And POSG.NOORPO = PO.NOORPO
Where PO.DAENTH > ?
BLOC_SQL;

    }

    public static function ChauffeursByDateColumns () {
        $columns = array();
        $columns['ch.noch']=array('data_type'=>'NUMERIC', 'length'=>5, 'text'=>"n° chauffeur");
        $columns['ch.morh']=array('data_type'=>'CHAR', 'length'=>10, 'text'=>"Mot de recherche");
        $columns['ch.rsti']=array('data_type'=>'CHAR', 'length'=>35, 'text'=>"Raison sociale tiers");
        $columns['ch.coempl']=array('data_type'=>'CHAR', 'length'=>12, 'text'=>"Code employé");
        return $columns;
    }

    public static function ChauffeursByDateQuery () {
        $columns = join(', ', array_keys(self::ChauffeursByDateColumns()));
        return <<<BLOC_SQL
Select
{$columns}
  , CH.NOCH as ID
From TESTCOM{SEPARATOR}CH CH
Where CH.NOCH In (
    Select PLCH.NOCHPL
    From TESTFIC{SEPARATOR}VY VY
    Join TESTFIC{SEPARATOR}PLCH PLCH
      On PLCH.NOPLCH = VY.NOVY
      And PLCH.AGPLCH = VY.COAGVY
    Where VY.DTDPVY > ?
  )
BLOC_SQL;

    }

    public static function VoyagesByDateColumns () {
        $columns = array();
        $columns['plch.nochpl']=array('data_type'=>'NUMERIC', 'length'=>5, 'text'=>"n° chauffeur");
        $columns['vy.novy']=array('data_type'=>'NUMERIC', 'length'=>7, 'text'=>"n° voyage");
        $columns['vy.dtdpvy']=array('data_type'=>'DATE', 'length'=>4, 'text'=>"DATE DEPART VOYAGE");
        $columns['vy.hhdpvy']=array('data_type'=>'NUMERIC', 'length'=>4, 'text'=>"Heure départ voyage");
        $columns['vy.dtarvy']=array('data_type'=>'DATE', 'length'=>4, 'text'=>"DATE FIN VOYAGE");
        $columns['vy.hharvy']=array('data_type'=>'NUMERIC', 'length'=>4, 'text'=>"Heure arrivée voyage");
        return $columns;
    }

    public static function VoyagesByDateQuery () {
        $columns = join(', ', array_keys(self::VoyagesByDateColumns()));
        return <<<BLOC_SQL
Select
{$columns}
  , PLCH.AGPLCH concat '-' concat
      digits(PLCH.NOPLCH) concat '-' concat
      digits(PLCH.NOCHPL) as id
From TESTFIC{SEPARATOR}VY VY
Join TESTFIC{SEPARATOR}PLCH PLCH
  On PLCH.NOPLCH = VY.NOVY
  And PLCH.AGPLCH = VY.COAGVY
Where VY.DTDPVY > ?
BLOC_SQL;

    }

    public static function VoyagesByChauffeurByDate() {
        return <<<BLOC_SQL
Select PLCH.NOCHPL as id_chf
  , trim(CH.RSTI) as nom_chf
  , VY.NOVY as id_voy
  , VY.DTDPVY as datdep
  , VY.HHDPVY as heudep
  , case when VY.DTARVY = '0001-01-01' THEN VY.DTDPVY ELSE VY.DTARVY END as datfin
  , case when VY.HHARVY = 0 THEN 1800 ELSE VY.HHARVY END as heufin
    From TESTFIC{SEPARATOR}VY VY
    Inner Join TESTFIC{SEPARATOR}PLCH PLCH
      On PLCH.NOPLCH = VY.NOVY
      And PLCH.AGPLCH = VY.COAGVY
    Inner Join TESTCOM{SEPARATOR}CH CH
  On CH.NOCH = VY.NOVY
  And PLCH.AGPLCH = VY.COAGVY
    Where VY.DTDPVY > ?
ORDER BY CH.RSTI, PLCH.NOCHPL, VY.DTDPVY, VY.HHDPVY, VY.DTARVY
BLOC_SQL;
    }
 }
