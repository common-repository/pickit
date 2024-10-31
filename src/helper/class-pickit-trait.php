<?php
namespace Ecomerciar\Pickit\Helper;

/**
 * Pickit Trait
 */
trait PickitTrait {
    
    public static function get_current_map_lightbox_url(){     
      return isset(self::get_map_lightbox_urls()[self::get_option('api-country')])? self::get_map_lightbox_urls()[self::get_option('api-country')] : '';
    }

    public static function get_map_lightbox_urls(){      
      if (self::is_sandbox()){
        return array(
          'AR' => 'https://lightbox.uat.pickit.com.ar/',         
          'CL' => 'https://lightbox.uat.pickit.cl/',
          'CO' => 'https://lightbox.uat.pickit.com.co/',      
          'ME' => 'https://lightbox.uat.pickit.com.mx/',
          'PE' => 'https://lightbox.uat.pickit.com.pe/',
          'UY' => 'https://lightbox.uat.pickit.com.uy/',      
        );
      } else {
        return array(
          'AR' => 'https://lightbox.pickit.com.ar/',         
          'CL' => 'https://lightbox.pickit.cl/',
          'CO' => 'https://lightbox.pickit.com.co/',      
          'ME' => 'https://lightbox.pickit.com.mx/',
          'PE' => 'https://lightbox.pickit.com.pe/',
          'UY' => 'https://lightbox.pickit.com.uy/',         
        );
      }

    }

    public static function remove_unwanted_chars($data){
      $unwanted_array = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
                            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
                            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
                            'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
      return strtr( $data, $unwanted_array );
    }

    /**
     * Get Countries list option
     *
     * @return Array
     */
    public static function get_countries(){        
        return array(
          'AR' => __( 'Argentina', 'woocommerce' ),         
          'CL' => __( 'Chile', 'woocommerce' ),
          'CO' => __( 'Colombia', 'woocommerce'),      
          'ME' => __( 'México', 'woocommerce'),  
          'PE' => __( 'Perú', 'woocommerce') ,
          'UY' => __( 'Uruguay', 'woocommerce' ),            
        );
      }

      public static function get_contact_href_by_country($countrycd){
        $hrefs = self::get_contact_href();
        return (isset($hrefs[$countrycd]))? $hrefs[$countrycd] : $hrefs['DFLT'];
      }
      
      public static function get_contact_href(){
        return array(
          'AR'  => 'contacto.ar@pickit.net',
          'CL'  => 'contacto.cl@pickit.net',
          'CO'  => 'contacto.co@pickit.net',
          'PE'  => 'contacto.pe@pickit.net',
          'ME'  => 'contacto.mx@pickit.net',
          'UY'  => 'contacto.uy@pickit.net',
          'DFLT' => '',
        );
      }

      public static function get_countries_address_labels(){
        return array(
          'AR' => array(
                  'address' => __( 'Calle', 'wc-pickit'),
                  'address-nbr' => __( 'Altura', 'wc-pickit'),
                  'address-city' => __( 'Ciudad', 'wc-pickit'),
                  'address-state' => __( 'Provincia', 'wc-pickit'),
                  'address-postalcode' => __( 'Código Postal', 'wc-pickit'),
                ),         
          'CL' => array(
                'address' => __( 'Calle', 'wc-pickit'),
                'address-nbr' => __( 'Número', 'wc-pickit'),
                'address-city' => __( 'Comuna', 'wc-pickit'),
                'address-state' => __( 'Región', 'wc-pickit'),
                'address-postalcode' => __( 'Código Postal', 'wc-pickit'),
              ),
          'CO' => array(
                'address' => __( 'Calle', 'wc-pickit'),
                'address-nbr' => __( 'Número', 'wc-pickit'),
                'address-city' => __( 'Municipio o Ciudad Capital', 'wc-pickit'),
                'address-state' => __( 'Departamento', 'wc-pickit'),
                'address-postalcode' => __( 'Código Postal', 'wc-pickit'),
              ),    
          'ME' => array(
                'address' => __( 'Calle', 'wc-pickit'),
                'address-nbr' => __( 'Número Exterior', 'wc-pickit'),
                'address-city' => __( 'Municipio / Alcaldía', 'wc-pickit'),
                'address-state' => __( 'Estado', 'wc-pickit'),
                'address-postalcode' => __( 'Código Postal', 'wc-pickit'),
              ),  
          'UY' => array(
                'address' => __( 'Calle', 'wc-pickit'),
                'address-nbr' => __( 'Número', 'wc-pickit'),
                'address-city' => __( 'Localidad', 'wc-pickit'),
                'address-state' => __( 'Departamento', 'wc-pickit'),
                'address-postalcode' => __( 'Código Postal', 'wc-pickit'),
              ),
          'DFLT' => array(
            'address' => __( 'Calle', 'wc-pickit'),
            'address-nbr' => __( 'Número', 'wc-pickit'),
            'address-city' => __( 'Ciudad', 'wc-pickit'),
            'address-state' => __( 'Provincia', 'wc-pickit'),
            'address-postalcode' => __( 'Código Postal', 'wc-pickit'),
          ),
            );
      }

      public static function translate_wc_state_to_pickit(string $country = '', string $state = ''){
        //Argentina
        $translate['AR']['C'] = "CABA";//'Ciudad Autónoma de Buenos Aires';
        $translateZone['AR']['B'] = "Buenos Aires";//'Buenos Aires';
        $translateZone['AR']['K'] = "Catamarca";//'Catamarca';
        $translateZone['AR']['H'] = "Chaco";//'Chaco';
        $translateZone['AR']['U'] = "Chubut";//'Chubut';
        $translateZone['AR']['X'] = "Córdoba";//'Córdoba';
        $translateZone['AR']['W'] = "Corrientes";//'Corrientes';
        $translateZone['AR']['E'] = "Entre Ríos";//'Entre Ríos';
        $translateZone['AR']['P'] = "Formosa";//'Formosa';
        $translateZone['AR']['Y'] = "Jujuy";//'Jujuy';
        $translateZone['AR']['L'] = "La Pampa";//'La Pampa';
        $translateZone['AR']['F'] = "La Rioja";//'La Rioja';
        $translateZone['AR']['M'] = "Mendoza";//'Mendoza';
        $translateZone['AR']['N'] = "Misiones";//'Misiones';
        $translateZone['AR']['Q'] = "Neuquén";//'Neuquén';
        $translateZone['AR']['R'] = "Río Negro";//'Río Negro';
        $translateZone['AR']['A'] = "Salta";//'Salta';
        $translateZone['AR']['J'] = "San Juan";//'San Juan';
        $translateZone['AR']['D'] = "San Luis";//'San Luis';
        $translateZone['AR']['Z'] = "Santa Cruz";//'Santa Cruz';
        $translateZone['AR']['S'] = "Santa Fe";//'Santa Fe';
        $translateZone['AR']['G'] = "Santiago del Estero";//'Santiago del Estero';
        $translateZone['AR']['V'] = "Tierra del Fuego";//'Tierra del Fuego';
        $translateZone['AR']['T'] = "Tucumán";//'Tucumán';
        
        //Mexico
        $translateZone['ME']['DF'] = "Ciudad de México";//'Ciudad de Mexico';
        $translateZone['ME']['JA'] = "Jalisco";//'Jalisco';
        $translateZone['ME']['NL'] = "Nuevo León";//'Nuevo León';
        $translateZone['ME']['AG'] = "Aguascalientes";//'Aguascalientes';
        $translateZone['ME']['BC'] = "Baja California";//'Baja California';
        $translateZone['ME']['BS'] = "Baja California Sur";//'Baja California Sur';
        $translateZone['ME']['CM'] = "Campeche";//'Campeche';
        $translateZone['ME']['CS'] = "Chiapas";//'Chiapas';
        $translateZone['ME']['CH'] = "Chihuahua";//'Chihuahua';
        $translateZone['ME']['CO'] = "Coahuila de Zaragoza";//'Coahuila';
        $translateZone['ME']['CL'] = "Colima";//'Colima';
        $translateZone['ME']['DG'] = "Durango";//'Durango';
        $translateZone['ME']['GT'] = "Guanajuato";//'Guanajuato';
        $translateZone['ME']['GR'] = "Guerrero";//'Guerrero';
        $translateZone['ME']['HG'] = "Hidalgo";//'Hidalgo';
        $translateZone['ME']['MX'] = "Estado de México"; //'Estado de México';
        $translateZone['ME']['MI'] = "Michoacán de Ocampo";//'Michoacán';
        $translateZone['ME']['MO'] = "Morelos";//'Morelos';
        $translateZone['ME']['NA'] = "Nayarit";//'Nayarit';
        $translateZone['ME']['OA'] = "Oaxaca";//'Oaxaca';
        $translateZone['ME']['PU'] = "Puebla";//'Puebla';
        $translateZone['ME']['QR'] = "Quintana Roo";//'Quintana Roo';
        $translateZone['ME']['QT'] = "Querétaro";//'Querétaro';
        $translateZone['ME']['SL'] = "San Luis Potosí";//'San Luis Potosí';
        $translateZone['ME']['SI'] = "Sinaloa";//'Sinaloa';
        $translateZone['ME']['SO'] = "Sonora";//'Sonora';
        $translateZone['ME']['TB'] = "Tabasco";//'Tabasco';
        $translateZone['ME']['TM'] = "Tamaulipas";//'Tamaulipas';
        $translateZone['ME']['TL'] = "Tlaxcala";//'Tlaxcala';
        $translateZone['ME']['VE'] = "Veracruz de Ignacio de la Llave";//'Veracruz';
        $translateZone['ME']['YU'] = "Yucatán";//'Yucatán';
        $translateZone['ME']['ZA'] = "Zacatecas";//'Zacatecas';
                /*
        33,1,Azcapotzalco,MX-CMX,1,0
        34,1,Álvaro Obregón,MX-CMX,1,0
        35,1,Benito Juárez,MX-CMX,1,0
        36,1,Coyoacán,MX-CMX,1,0
        37,1,Gustavo A. Madero,MX-CMX,1,0
        38,1,Iztapalapa,MX-CMX,1,0
        39,1,La Magdalena Contreras,MX-CMX,1,0
        40,1,Tláhuac,MX-CMX,1,0
        41,1,Tlalpan,MX-CMX,1,0
        42,1,Venustiano Carranza,MX-CMX,1,0
        43,1,Xochimilco,MX-CMX,1,0*/


        //Peru
        $translateZone['PE']['CAL'] = "Callao";//'El Callao';
        /*$translateZone['PE']['LMA'] = 'Municipalidad Metropolitana de Lima';
        $translateZone['PE']['AMA'] = 'Amazonas';
        $translateZone['PE']['ANC'] = 'Ancash';
        $translateZone['PE']['APU'] = 'Apurímac';
        $translateZone['PE']['ARE'] = 'Arequipa';
        $translateZone['PE']['AYA'] = 'Ayacucho';
        $translateZone['PE']['CAJ'] = 'Cajamarca';
        $translateZone['PE']['CUS'] = 'Cusco';
        $translateZone['PE']['HUV'] = 'Huancavelica';
        $translateZone['PE']['HUC'] = 'Huánuco';
        $translateZone['PE']['ICA'] = 'Ica';
        $translateZone['PE']['JUN'] = 'Junín';
        $translateZone['PE']['LAL'] = 'La Libertad';
        $translateZone['PE']['LA'] = 'Lambayeque';*/
        $translateZone['PE']['LIM'] = "Lima";//'Lima';
        /*$translateZone['PE']['LOR'] = 'Loreto';
        $translateZone['PE']['MDD'] = 'Madre de Dios';
        $translateZone['PE']['MOQ'] = 'Moquegua';
        $translateZone['PE']['PAS'] = 'Pasco';
        $translateZone['PE']['PIU'] = 'Piura';
        $translateZone['PE']['PUN'] = 'Puno';
        $translateZone['PE']['SAM'] = 'San Martín';
        $translateZone['PE']['TAC'] = 'Tacna';
        $translateZone['PE']['TUM'] = 'Tumbes';
        $translateZone['PE']['UCA'] = 'Ucayali';*/

        /*    
        'CL' => __( 'Chile', 'woocommerce' ),
        'CO' => __( 'Colombia', 'woocommerce'),           
        'UY' => __( 'Uruguay', 'woocommerce' ),*/     
        if (isset($translateZone[$country]) && isset($translateZone[$country][$state])){
          return $translateZone[$country][$state];
        }
        return "";
      }

      public static function get_country_address_labels(string $country){
        return ( isset(self::get_countries_address_labels()[$country] ) )? self::get_countries_address_labels()[$country] : self::get_countries_address_labels()['DFLT'];
      }

      public static function get_point_for_state(string $country = '', string $state = ''){
        $points = self::get_option('points', []);
         $pickit_state = self::translate_wc_state_to_pickit($country, $state);
          if(isset($pickit_state) && !empty($pickit_state)){
            foreach ($points as $key => $val) {
              if ($val['province'] === $pickit_state) {
                  return $points[$key];
              }
          }
        }        
        return (count($points)>0)? $points[0]: [];
      }

      public static function get_points(){
        $ret = [];
        $points = self::get_option('points', []);
        foreach($points as $point){
          $ret[$point['idService']] = [ 'name'=> $point['name'], 'address' => $point['address']] ;
        }
        return $ret;
      }

      public static function get_status(){
        if ( "AR" === self::get_option('api-country')) {
          return [            
              'processing' => [
                'title' => 'Procesando',
                'subtitle' => 'Procesando',  
              ],
              'cancelled' => [
                'title' => 'Cancelado',
                'subtitle' => 'Tu envío fue cancelado manualmente',
              ], 
              'canceledOnPickit' => [
                'title' => 'Cancelado',
                'subtitle' => 'Tu envío fue cancelado manualmente',
              ], 
              'restored' =>  [
                'title' => 'Restaurado',
                'subtitle' => 'Tu envío ha sido restaurado corretamente',
              ],     
              'error' => [
                'title' => '(el error code que corresponda del back)',
                'subtitle' => '',
              ], 
              'labelCreated' => [
                'title' => 'Etiqueta Creada',
                'subtitle' => 'Has creado con exito tu etiqueta',
              ], 
              'pending' => [
                'title' => 'Pendiente',
                'subtitle' => 'Debes crear tu etiqueta para avanzar',
              ],
              'pendingPaymentApproval' => [
                'title' => 'Pago Aprobado',
                'subtitle' => 'Tu pago se realizó correctamente',
              ],     
              'PaymentRejected' => [
                'title' => 'Pago Rechazado',
                'subtitle' => 'Tu pago fue rechazo, vuelve a intentarlo',
              ], 
              'pendingPayment' => [
                'title' => 'Pago Pendiente',
                'subtitle' => 'Debes pagar por tu etiqueta para continuar',
              ],
              'forRegister' => [
                'title' => 'En proceso de registro',
                'subtitle' => 'tu envio esta siendo registrado',
              ],
              'courier' => [
                'title' => 'Recepcionado',
                'subtitle' => 'Tenemos tu paquete, pronto haremos la entrega',
              ],  
              'delivered' => [
                'title' => 'Entregado',
                'subtitle' => 'Tu paquete fue entregado correctamente',
              ],
              'inDropOffPoint' => [
                'title' => 'En Punto Drop Off',
                'subtitle'=> 'Tu paquete se encuentra en el punto dropoff pronto lo recogeremos',
              ],
              'availableForDrop' => [
                'title' => 'Listo para ser retirado',
                'subtitle' => 'Tu paquete se encuentra listo para ser recogido por pickit',
              ], 
              'inPikcitPoint' => [
                'title' => 'En Punto Pickit',
                'subtitle' => 'Tu paquete se encuentra listo para retirar en el punto seleccionado',
              ], 
              'inRetailer' => [
                'title' => 'En Retailer',
                'subtitle' => 'Listo para ser retirado',
              ], 
              'returnToSender' => [
                'title' => 'Devolución a Remitente	',
                'subtitle' => 'Devolución a Remitente',
              ],    
              'returnedToSender' => [
                'title' => 'Devuelto a Remitente',
                'subtitle' => 'Devuelto a Remitente',
              ],
              'expired' => [ 
                'title' => 'Vencido',
                'subtitle'=> 'Vencido',
              ],
              'availableForDropRetailer' => [
                'title'=> 'En Retailer',
                'subtitle'=> 'Disponible para Colecta',
              ], 
              'free' => [
                'title'=> 'Liberada',
                'subtitle' => 'Liberada',
              ],  
              'crossdock' => [
                'title' => 'En XD',
                'subtitle' => 'En XD',
              ],
              'pointDistribution' => [
                'title' => 'Distribución a Punto',
                'subtitle' => 'Distribución a Punto',
              ],    
              'homeDistribution' => [
                'title' => 'Distribución a Domicilio',
                'subtitle'=> 'Distribución a Domicilio',
              ], 
              'notDelivery'=> [
                'title' => 'Visita no Entregado',
                'subtitle' => 'Visita no Entregado',
              ],
              'lost' => [
                'title' => 'Siniestrado',
                'subtitle' => 'Siniestrado',
              ]
            ] ;
        } else {
          return [
            'processing' => [
              'title' => 'Procesando',
              'subtitle' => 'Procesando',
            ], 
            'cancelled' => [
              'title' => 'Cancelado',
              'subtitle' => 'Tu envío fue cancelado manualmente',
            ],
            'canceledOnPickit'=> [
              'title' => 'Cancelado',
              'subtitle' => 'Tu envío fue cancelado manualmente',
            ],
            'restored' => [
              'title' => 'Restaurado',
              'subtitle' => 'Tu envío ha sido restaurado corretamente',
            ],
            'error'=> [
              'title' => '(el error code que corresponda del back)',
              'subtitle'=> '',
            ],
            'labelCreated' => [
              'title' => 'Etiqueta Creada',
              'subtitle' => 'Has creado con exito tu etiqueta',
            ],
            'pending' => [
              'title' => 'Pendiente',
              'subtitle' => 'Debes crear tu etiqueta para avanzar',  
            ],
            'pendingPaymentApproval' => [
              'title' => 'Pago Aprobado',
              'subtitle' => 'Tu pago se realizó correctamente',
            ],
            'PaymentRejected' => [    
              'title' => 'Pago Rechazado',
              'subtitle' => 'Tu pago fue rechazo, vuelve a intentarlo',
            ],
            'pendingPayment' => [
              'title'=> 'Pago Pendiente',
              'subtitle'=> 'Debes pagar por tu etiqueta para continuar',
            ],
            'forRegister' => [
              'title' => 'En proceso de registro',
              'subtitle' => 'tu envio esta siendo registrado',
            ],
            'courier' => [
              'title'=> 'Recepcionado',
              'subtitle'=> 'Tenemos tu paquete, pronto haremos la entrega',
              ],
            'delivered' => [
              'title'=> 'Entregado',
              'subtitle'=> 'Tu paquete fue entregado correctamente',
              ],
            'inDropOffPoint' => [
              'title'=> 'En Punto Drop Off',
              'subtitle'=> 'Tu paquete se encuentra en el punto dropoff pronto lo recogeremos',
              ],
            'availableForDrop' => [
              'title'=> 'Listo para ser retirado',
              'subtitle'=> 'Tu paquete se encuentra listo para ser recogido por pickit',
              ],
            'inPikcitPoint' => [
              'title'=> 'En Punto Pickit',
              'subtitle'=> 'Tu paquete se encuentra listo para retirar en el punto seleccionado',
              ],
            'inRetailer' => [
              'title'=> 'En Retailer',
              'subtitle'=> 'Listo para ser retirado',
              ],
            'point' => [
              'title'=> 'Punto',
              'subtitle'=> 'Punto',
              ],
            'lastMile' => [
              'title'=> 'Ultima Milla',
              'subtitle'=> 'Ultima Milla',
              ],
            'returnedToSender' => [
              'title'=> 'Devuelto a Remitente',
              'subtitle'=> 'Devuelto a Remitente',
              ],
            'expired' => [
              'title'=> 'Vencido',
              'subtitle'=> 'Vencido',
              ],
            'lost' => [
              'title'=> 'Siniestrado',
              'subtitle'=> 'Siniestrado',
              ],
            'availableForDropRetailer' => [
              'title'=> 'Disponible para Colecta',
              'subtitle'=> 'Disponible para Colecta',
              ],
            'availableForDropDropoff' => [
              'title'=> 'Disponible para Colecta',
              'subtitle'=> 'Disponible para Colecta',
              ],
            'waiting' => [
              'title'=> 'En Espera',
              'subtitle'=> 'En Espera',
              ],
            'retired' => [
              'title'=> 'Retirado',
              'subtitle'=> 'Retirado',
              ],
            'crossdock' => [
              'title'=> 'En XD',
              'subtitle'=> 'En XD',
              ],
            'delivery' => [
              'title'=> 'En Entrega',
              'subtitle'=> 'En Entrega',
              ],
            'pointDistribution' => [
              'title'=> 'Distribución a punto',
              'subtitle'=> 'Distribución a punto',
              ],
            'refundRetailer' => [
              'title'=> 'En devolución',
              'subtitle'=> 'En devolución',
              ],
            'returnedToSenderPoint' => [
              'title'=> 'Devuelto',
              'subtitle'=> 'Devuelto',
              ],
            'availableForDropPoint' => [
              'title'=> 'Disponible para Colecta',
              'subtitle'=> 'Disponible para Colecta',
              ],
            'availableForCollect' => [
              'title'=> 'Disponible para Retiro',
              'subtitle'=> 'Disponible para Retiro',
              ],
            'inRetired' => [
              'title'=> 'En retiro',
              'subtitle'=> 'En retiro',
              ],
            'deliveryLastMile' => [
              'title'=> 'En Entrega',
              'subtitle'=> 'En Entrega',
              ],
            'refund' => [
              'title'=> 'Devolucion',
              'subtitle'=> 'Devolucion',
              ],
            'returnedToSenderRetailer' => [
              'title'=> 'Firmado',
              'subtitle'=> 'Firmado',
              ]            
            ];
        }  
      }      
  
}