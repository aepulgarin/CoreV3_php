<?php
/**
 * Explicacion corta del contenido de archivos y funciones
 *
 * @author       Alvaro Pulgarin <aepulgarin@lebon.com.co>
 * @copyright    Alvaro Pulgarin Y-M-D
 * @category     Area
 * @package      Modulo
 * @subpackage   SubModulo
 * @version         Version
 */
class base extends mainModel {  

	public $nombre;
	public $nit;
	public $autoretiene;
	public $ultimo_cierre;
	public $f_cierrei;
	public $c_cartera;
	public $standard;
	public $sistema_op;
	public $tarifa_fletes;
	public $c_autoretencion;
	public $tipo_facturacion;
	public $tipo_contribuyente;
	public $recefac;
	public $comprar;
	public $ica;
	public $nit_dian;
	public $presup;
	public $costeo_almacen;
	public $inve1;
	public $moneda;
	public $online;
	public $clase_aportante;
	public $forma_presentacion;
	public $nit_iss;
	public $riesgos;
	public $cta_tesoreria;
	public $seguridad;
	public $linea_fletes;
	public $inv_cronologico;
	public $f_cierred;
	public $currency;
	public $pais;
	public $prov_nacionales;
	public $prov_exterior;
	public $fmtfecha;
	public $alan;
	public $alm_origen;
	public $idioma;
	public $auditoria;
	public $ult_cie_cam;
	public $estado_erp;
	public $descripcion;  
	public $linea;  
	public $unidad;  
	public $tipo_inv;  
	public $pisotecho;  
	public $iva;  
	public $hoy;
	public $tabla;
	                                                                    
	public function __construct()  
	{  
		$this->Conectarse();
		$this->ejecuta_query("SET ISOLATION TO DIRTY READ");
	}
	public function getIden(){
		$m=$this->lee_uno("SELECT *, today as hoy FROM iden");
		foreach ($m as $key => $value) {
			$this->$key=trim($value);
		}
	}
	public function getSiglas(){
		$m=$this->lee_todo("SELECT valor,observacion,transaccion from ins_siglas_tablas where tabla='{$this->tabla}' and campo='{$this->campo}' and transaccion='{$this->transaccion}' order by valor");
		return $m;
	}
}  
