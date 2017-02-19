<?php

namespace API;

class V1 {
	
	public function holaMundo () {		
		return "hola Mundo!";
	}
	
}

namespace API\V1;

class Prestamos {
	
	public $importePrestamo;
	public $interesPrestamo;
	public $totalCuotas;
	public $cuotaMensual;

	private $interesPrestamoMensual;
	private $totalPagado;
	private $totalIntereses;
	
	function calcularPrestamo($importePrestamo=0, $totalCuotas=0, $interesPrestamo=0){
		
		$this->importePrestamo=$importePrestamo;
		$this->interesPrestamo=$interesPrestamo;
		
		if($importePrestamo*$interesPrestamo*$totalCuotas > 0){
			$this->totalCuotas = $totalCuotas;
			$this->interesPrestamoMensual=$interesPrestamo/1200;
			$this->cuotaMensual=$importePrestamo*($this->interesPrestamoMensual/(1-pow(1+$this->interesPrestamoMensual,-$this->totalCuotas)));
			$this->totalPagado=$this->cuotaMensual * $this->totalCuotas;
			$this->totalIntereses=$this->totalPagado-$importePrestamo;
		}else{
			$this->cuotaMensual=0;
			$this->totalCuotas=0;
			$this->interesPrestamoMensual=0;
			$this->totalPagado=0;
			$this->totalIntereses=0;
		}
		
		return $this->toArray();
	}

	function getCoutaMensual(){		
		return sprintf("%01.2f",$this->cuotaMensual);
	}
	
	function toArray() {
		return get_object_vars($this);
	}
}



class Inmuebles {

	function obtenerInmuebles () {
		$product = \Inmuebles::all();
		return $product->toArray();
	}
	
	
	function obtenerInmueble ($idInmueble) {
		$product = \Product::find($idInmueble);
		return $product->toArray();
	}
	
	function obtenerCercanos ($idInmueble) {
		
		$product = \InmueblesDistancias::query()->where("idInmueble", "=", $idInmueble)->get();
		return $product->toArray();
	}
	
	function obtenerCercanosDistancia ($idInmueble, $distancia) {
	
		$product = \InmueblesDistancias::query()->where("idInmueble", "=", $idInmueble)->where ("distancia", "<", $distancia)->get();
		return $product->toArray();
	}
	
	
}

?>