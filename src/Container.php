<?php
namespace Simcify;

use PDO;
use PHPMailer\PHPMailer\PHPMailer;
use DI\ContainerBuilder;
use function DI\factory;
use function DI\get;
use function DI\object as di_object;

final class Container {
    /**
     * Hold the class instance
     * 
     * @var \DI\Container
     */
    private static $__instance;
    
    /**
     * Create a new container object
     * 
     * @return  void
     */
    private function __construct() {
        // Expensive stuff
    }
   
    /**
     * Get the instance of the container
     * 
     * @return  \DI\Container
     */
    public static function getInstance() {
      if ( empty(static::$__instance) ) {
        static::$__instance = static::_makeContainer();
      }
   
      return static::$__instance;
    }
   
    /**
     * Set the instance of the container
     * 
     * @param  \DI\Container    $container
     * @return  void
     */
    /**
     * Load environment variables
     * 
     * @return  \DI\Container
     */
    protected static function _makeContainer() {
        $config = Config::cache();
        $dbConfig = $config['database'];
        // Create our new php-di container
        $builder = new ContainerBuilder();
        $builder->useAutowiring(true);
        $builder->addDefinitions([
            'config'            => $config,
            PDO::class          => di_object()->constructor(
                "mysql:host={$dbConfig['host']};dbname={$dbConfig['database']}",
                $dbConfig['username'],
                $dbConfig['password'],
                ['ATTR_DEFAULT_FETCH_MODE' => PDO::FETCH_OBJ]
            ),
            PHPMailer::class    => di_object()->constructor(true),
            'Simcify\Mailer'    => factory(function($mail) {
                $mail->SMTPDebug = 0;
                if (env("SMTP_AUTH")) {
                    $mail->isSMTP();
                }
                $mail->Host = env('SMTP_HOST');
                $mail->SMTPAuth = env("SMTP_AUTH");
                $mail->Username = env('MAIL_USERNAME');
                $mail->Password = env('SMTP_PASSWORD');
                $mail->SMTPSecure = env('MAIL_ENCRYPTION');
                $mail->Port = env('SMTP_PORT');
                return $mail;
            })->parameter('mail', get('PHPMailer\PHPMailer\PHPMailer')),
            Session::class      => di_object()
        ]);
        
        return $builder->build();
    }
}
