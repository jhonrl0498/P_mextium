<?php
// Configuración de la base de datos

class UsuarioModel {
    private $pdo;

    public function __construct() {
        require_once __DIR__ . '/conexion.php'; // Usa la conexión centralizada
        $this->pdo = $pdo;
    }
    
    /**
     * Registrar nuevo usuario
     */
    public function registrarUsuario($datos) {
        try {
            // Limitar registros por IP (máx 3 por 24h)
            $ip = $_SERVER['REMOTE_ADDR'] ?? '';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $email = $datos['email'] ?? '';
            $timestamp = date('Y-m-d H:i:s');
            $log_status = 'ok';
            $log_message = '';

            // Protección fuerza bruta: bloqueo tras 5 intentos fallidos en 1 hora
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM registro_logs WHERE ip = ? AND status = 'fail' AND fecha >= (NOW() - INTERVAL 1 HOUR)");
            $stmt->execute([$ip]);
            $fallos = $stmt->fetchColumn();
            if ($fallos >= 5) {
                return [
                    'success' => false,
                    'message' => 'Demasiados intentos fallidos de registro. Intenta nuevamente en 1 hora.'
                ];
            }

            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE ip_registro = ? AND fecha_registro >= (NOW() - INTERVAL 1 DAY)");
            $stmt->execute([$ip]);
            $registrosIp = $stmt->fetchColumn();
            if ($registrosIp >= 3) {
                $log_status = 'fail';
                $log_message = 'Limite IP';
                $this->logRegistro($ip, $email, $userAgent, $timestamp, $log_status, $log_message);
                return [
                    'success' => false,
                    'message' => 'Has alcanzado el límite de registros permitidos desde tu IP en 24 horas.'
                ];
            }

            // Validar que el email no exista
            if ($this->emailExiste($email)) {
                $log_status = 'fail';
                $log_message = 'Email existe';
                $this->logRegistro($ip, $email, $userAgent, $timestamp, $log_status, $log_message);
                return [
                    'success' => false,
                    'message' => 'Este correo electrónico ya está registrado'
                ];
            }

            // Validar que la cédula no exista
            if ($this->cedulaExiste($datos['cedula'])) {
                $log_status = 'fail';
                $log_message = 'Cédula existe';
                $this->logRegistro($ip, $email, $userAgent, $timestamp, $log_status, $log_message);
                return [
                    'success' => false,
                    'message' => 'Esta cédula ya está registrada'
                ];
            }

            // Hash de la contraseña
            $passwordHash = password_hash($datos['password'], PASSWORD_DEFAULT);
            // Generar token de activación
            $token = bin2hex(random_bytes(32));
            // Insertar usuario como pendiente (no verificado)
            $stmt = $this->pdo->prepare("
                INSERT INTO usuarios (nombre, apellido, email, telefono, direccion, password, cedula, rol_id, ip_registro, fecha_registro, estado, token_activacion) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'pendiente', ?)
            ");
            $resultado = $stmt->execute([
                $datos['nombre'],
                $datos['apellido'],
                $email,
                $datos['telefono'],
                $datos['direccion'],
                $passwordHash,
                $datos['cedula'],
                $datos['rol_id'],
                $ip,
                $token
            ]);

            if ($resultado) {
                $log_status = 'ok';
                $log_message = 'Registro exitoso';
                $this->logRegistro($ip, $email, $userAgent, $timestamp, $log_status, $log_message);
                // Enviar email de activación
                $this->enviarEmailActivacion($email, $token);
                return [
                    'success' => true,
                    'message' => 'Usuario registrado exitosamente. Revisa tu correo para activar la cuenta.',
                    'user_id' => $this->pdo->lastInsertId()
                ];
            } else {
                $log_status = 'fail';
                $log_message = 'Error SQL';
                $this->logRegistro($ip, $email, $userAgent, $timestamp, $log_status, $log_message);
                return [
                    'success' => false,
                    'message' => 'Error al registrar el usuario'
                ];
            }
        } catch (PDOException $e) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $email = $datos['email'] ?? '';
            $timestamp = date('Y-m-d H:i:s');
            $this->logRegistro($ip, $email, $userAgent, $timestamp, 'fail', 'PDO: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error del sistema: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Enviar email de activación de cuenta
     */
    private function enviarEmailActivacion($email, $token) {
        $subject = 'Activa tu cuenta en Mextium';
        $activationLink = 'https://TU_DOMINIO.com/activar.php?token=' . urlencode($token);
        $message = "<h2>Bienvenido a Mextium</h2><p>Para activar tu cuenta, haz clic en el siguiente enlace:</p><p><a href='$activationLink'>$activationLink</a></p>";
        // Usa mail() o PHPMailer según tu configuración
        // mail($email, $subject, $message, "Content-type: text/html; charset=utf-8");
        // Si usas PHPMailer, aquí puedes integrar el envío real
    }

    /**
     * Log de intentos de registro
     */
    private function logRegistro($ip, $email, $userAgent, $timestamp, $status, $message) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO registro_logs (ip, email, user_agent, fecha, status, mensaje) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$ip, $email, $userAgent, $timestamp, $status, $message]);
        } catch (PDOException $e) {
            // No interrumpir el registro si falla el log
        }
    }
    
    /**
     * Iniciar sesión
     */
    public function iniciarSesion($email, $password) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT u.id, u.nombre, u.apellido, u.email, u.password, u.rol_id, u.estado,
                       r.nombre as rol_nombre
                FROM usuarios u 
                JOIN roles r ON u.rol_id = r.id 
                WHERE u.email = ?
            ");
            
            $stmt->execute([$email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($usuario && password_verify($password, $usuario['password'])) {
                // Permitir acceso a cualquier usuario registrado, el bloqueo se hace en acciones de compra/venta
                
                // Actualizar último acceso
                $this->actualizarUltimoAcceso($usuario['id']);
                
                return [
                    'success' => true,
                    'message' => 'Inicio de sesión exitoso',
                    'user' => [
                        'id' => $usuario['id'],
                        'nombre' => $usuario['nombre'],
                        'apellido' => $usuario['apellido'],
                        'email' => $usuario['email'],
                        'rol_id' => $usuario['rol_id'],
                        'rol_nombre' => $usuario['rol_nombre']
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Credenciales incorrectas'
                ];
            }
            
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error del sistema: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Obtener usuario por ID
     */
    public function obtenerUsuarioPorId($id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT u.*, r.nombre as rol_nombre 
                FROM usuarios u 
                LEFT JOIN roles r ON u.rol_id = r.id 
                WHERE u.id = ?
            ");
            
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error al obtener usuario por ID: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener usuario por email
     */
    public function obtenerUsuarioPorEmail($email) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT u.*, r.nombre as rol_nombre 
                FROM usuarios u 
                JOIN roles r ON u.rol_id = r.id 
                WHERE u.email = ?
            ");
            
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Obtener usuario por token de activación
     */
    public function obtenerUsuarioPorTokenActivacion($token) {
        try {
            $stmt = $this->pdo->prepare("SELECT id, estado FROM usuarios WHERE token_activacion = ?");
            $stmt->execute([$token]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Activar usuario por ID
     */
    public function activarUsuarioPorId($id) {
        try {
            $stmt = $this->pdo->prepare("UPDATE usuarios SET estado = 'activo', token_activacion = NULL WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Actualizar perfil de usuario
     */
    public function actualizarPerfil($datos) {
        try {
            // DEBUG TEMPORAL - QUITAR EN PRODUCCIÓN
            error_log("Datos recibidos en actualizarPerfil: " . print_r($datos, true));
            
            // Verificar que el ID esté presente
            if (!isset($datos['id']) || empty($datos['id'])) {
                error_log("Error: ID de usuario no presente");
                throw new Exception("ID de usuario es requerido");
            }

            // Preparar campos a actualizar
            $campos = [];
            $valores = [];
            
            if (isset($datos['nombre']) && !empty($datos['nombre'])) {
                $campos[] = "nombre = ?";
                $valores[] = $datos['nombre'];
            }
            
            if (isset($datos['apellido']) && !empty($datos['apellido'])) {
                $campos[] = "apellido = ?";
                $valores[] = $datos['apellido'];
            }
            
            if (isset($datos['email']) && !empty($datos['email'])) {
                $campos[] = "email = ?";
                $valores[] = $datos['email'];
            }
            
            if (isset($datos['telefono'])) {
                $campos[] = "telefono = ?";
                $valores[] = $datos['telefono'];
            }
            
            if (isset($datos['direccion'])) {
                $campos[] = "direccion = ?";
                $valores[] = $datos['direccion'];
            }
            
            // Agregar fecha de actualización
            $campos[] = "fecha_actualizacion = CURRENT_TIMESTAMP";
            
            // Agregar ID al final
            $valores[] = $datos['id'];
            
            if (count($campos) <= 1) { // Solo fecha_actualizacion
                error_log("Error: No hay campos para actualizar");
                throw new Exception("No hay campos para actualizar");
            }
            
            $sql = "UPDATE usuarios SET " . implode(", ", $campos) . " WHERE id = ?";
            error_log("SQL a ejecutar: " . $sql);
            error_log("Valores: " . print_r($valores, true));
            
            $stmt = $this->pdo->prepare($sql);
            $resultado = $stmt->execute($valores);
            
            error_log("Resultado de la actualización: " . ($resultado ? 'true' : 'false'));
            error_log("Filas afectadas: " . $stmt->rowCount());
            
            return $resultado;
            
        } catch (PDOException $e) {
            error_log("Error de base de datos al actualizar perfil: " . $e->getMessage());
            error_log("SQL Error Code: " . $e->getCode());
            return false;
        } catch (Exception $e) {
            error_log("Error al actualizar perfil: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Cambiar contraseña
     */
    public function cambiarPassword($id, $passwordActual, $passwordNuevo) {
        try {
            // Verificar contraseña actual
            $stmt = $this->pdo->prepare("SELECT password FROM usuarios WHERE id = ?");
            $stmt->execute([$id]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$usuario || !password_verify($passwordActual, $usuario['password'])) {
                return [
                    'success' => false,
                    'message' => 'La contraseña actual es incorrecta'
                ];
            }
            
            // Actualizar contraseña
            $passwordHash = password_hash($passwordNuevo, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
            $resultado = $stmt->execute([$passwordHash, $id]);
            
            return [
                'success' => $resultado,
                'message' => $resultado ? 'Contraseña actualizada exitosamente' : 'Error al actualizar contraseña'
            ];
            
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error del sistema: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Obtener todos los roles
     */
    public function obtenerRoles() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM roles ORDER BY id");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Verificar si email existe
     */
    private function emailExiste($email) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Verificar si cédula existe
     */
    private function cedulaExiste($cedula) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE cedula = ?");
        $stmt->execute([$cedula]);
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Actualizar último acceso
     */
    private function actualizarUltimoAcceso($id) {
        try {
            $stmt = $this->pdo->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?");
            $stmt->execute([$id]);
        } catch (PDOException $e) {
            // Log error but don't fail login
        }
    }
    
    /**
     * Validar datos de registro
     */
    public function validarDatosRegistro($datos) {
        $errores = [];
        
        // Palabras prohibidas (puedes agregar más)
        $palabrasProhibidas = [
            'admin', 'root', 'test', 'prueba', 'spam', 'ofensivo', 'tonto', 'idiota', 'maldito', 'xxx', 'fake', 'banned', 'prohibido', 'inapropiado', 'badword'
        ];
        $nombre = strtolower($datos['nombre'] ?? '');
        $apellido = strtolower($datos['apellido'] ?? '');
        foreach ($palabrasProhibidas as $palabra) {
            if (strpos($nombre, $palabra) !== false || strpos($apellido, $palabra) !== false) {
                $errores[] = 'El nombre o apellido contiene palabras no permitidas.';
                break;
            }
        }
        // Validar nombre
        if (empty($datos['nombre']) || strlen($datos['nombre']) < 2) {
            $errores[] = 'El nombre debe tener al menos 2 caracteres';
        }
        // Validar apellido
        if (empty($datos['apellido']) || strlen($datos['apellido']) < 2) {
            $errores[] = 'El apellido debe tener al menos 2 caracteres';
        }
        
        // Validar email
        if (empty($datos['email']) || !filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'Ingresa un correo electrónico válido';
        } else {
            // Bloquear correos desechables
            $email = strtolower($datos['email']);
            $temp_domains = [
                'mailinator.com', 'yopmail.com', '10minutemail.com', 'guerrillamail.com', 'tempmail.com',
                'getnada.com', 'trashmail.com', 'fakeinbox.com', 'mintemail.com', 'dispostable.com',
                'sharklasers.com', 'spamgourmet.com', 'maildrop.cc', 'mytemp.email', 'throwawaymail.com',
                'emailondeck.com', 'moakt.com', 'mailcatch.com', 'mailnesia.com', 'openmailbox.org',
                'tmail.ws', 'jetable.org', 'anonbox.net', 'spam4.me', 'mail-temp.com', 'temp-mail.org'
            ];
            $domain = substr(strrchr($email, '@'), 1);
            if (in_array($domain, $temp_domains)) {
                $errores[] = 'No se permiten correos electrónicos temporales';
            }
        }
        
        // Validar teléfono: solo números
        if (empty($datos['telefono']) || !preg_match('/^[0-9]+$/', $datos['telefono'])) {
            $errores[] = 'El teléfono debe contener solo números';
        }

        // Validar cédula: solo números
        if (empty($datos['cedula']) || !preg_match('/^[0-9]+$/', $datos['cedula'])) {
            $errores[] = 'La cédula debe contener solo números';
        }
        
        // Validar dirección
        if (empty($datos['direccion']) || strlen($datos['direccion']) < 10) {
            $errores[] = 'La dirección debe tener al menos 10 caracteres';
        }
        
        // Validar contraseña
        if (empty($datos['password']) || strlen($datos['password']) < 6) {
            $errores[] = 'La contraseña debe tener al menos 6 caracteres';
        }
        
        // Validar rol
        if (empty($datos['rol_id']) || !in_array($datos['rol_id'], [1, 2, 3])) {
            $errores[] = 'Selecciona un tipo de cuenta válido';
        }
        
        return $errores;
    }
    
    /**
     * Crear token de recuperación de contraseña
     */
    public function crearTokenRecuperacion($email, $token, $expiracion) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM reseteo_contraseñas WHERE email = ?");
            $stmt->execute([$email]);

            $stmt = $this->pdo->prepare("
                INSERT INTO reseteo_contraseñas (email, token, fecha_expiracion, usado)
                VALUES (?, ?, ?, 0)
            ");
            $ok = $stmt->execute([$email, $token, $expiracion]);
            if (!$ok) {
                error_log("Error SQL: " . implode(" | ", $stmt->errorInfo()));
            }
            return $ok;
        } catch (PDOException $e) {
            error_log("Error al crear token de recuperación: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Validar token de recuperación
     */
    public function validarTokenRecuperacion($token) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT email, fecha_expiracion, usado 
                FROM reseteo_contraseñas 
                WHERE token = ?
            ");
            
            $stmt->execute([$token]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $ahora = new DateTime();
                $expiracion = new DateTime($resultado['fecha_expiracion']);
                
                if ($expiracion > $ahora && !$resultado['usado']) {
                    return [
                        'valido' => true,
                        'email' => $resultado['email']
                    ];
                }
            }
            
            return ['valido' => false];
            
        } catch (PDOException $e) {
            return ['valido' => false];
        }
    }
    
    /**
     * Resetear contraseña con token
     */
    public function resetearContraseñaConToken($token, $nuevaContraseña) {
        try {
            $validacion = $this->validarTokenRecuperacion($token);
            
            if (!$validacion['valido']) {
                return [
                    'success' => false,
                    'message' => 'El enlace de recuperación es inválido o ha expirado'
                ];
            }
            
            $email = $validacion['email'];
            
            $this->pdo->beginTransaction();
            
            $passwordHash = password_hash($nuevaContraseña, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("UPDATE usuarios SET password = ? WHERE email = ?");
            $resultadoUsuario = $stmt->execute([$passwordHash, $email]);
            
            $stmt = $this->pdo->prepare("UPDATE reseteo_contraseñas SET usado = TRUE WHERE token = ?");
            $resultadoToken = $stmt->execute([$token]);
            
            if ($resultadoUsuario && $resultadoToken) {
                $this->pdo->commit();
                return [
                    'success' => true,
                    'message' => 'Tu contraseña ha sido actualizada exitosamente'
                ];
            } else {
                $this->pdo->rollback();
                return [
                    'success' => false,
                    'message' => 'Error al actualizar la contraseña'
                ];
            }
            
        } catch (PDOException $e) {
            $this->pdo->rollback();
            return [
                'success' => false,
                'message' => 'Error del sistema: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Obtener contraseña del usuario para validación
     */
    public function obtenerContraseñaUsuario($id) {
        try {
            // CAMBIAR 'contraseña' por 'password' que es como está en tu tabla
            $stmt = $this->pdo->prepare("SELECT password FROM usuarios WHERE id = ?");
            $stmt->execute([$id]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ? $resultado['password'] : false;
            
        } catch (PDOException $e) {
            error_log("Error al obtener contraseña: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Cambiar contraseña de usuario
     */
    public function cambiarContraseña($userId, $nuevaContraseña) {
        try {
            $contraseñaHash = password_hash($nuevaContraseña, PASSWORD_DEFAULT);
            
            // CAMBIAR 'contraseña' por 'password' que es como está en tu tabla
            $stmt = $this->pdo->prepare("
                UPDATE usuarios 
                SET password = ?, fecha_actualizacion = CURRENT_TIMESTAMP 
                WHERE id = ?
            ");
            
            return $stmt->execute([$contraseñaHash, $userId]);
            
        } catch (PDOException $e) {
            error_log("Error al cambiar contraseña: " . $e->getMessage());
            return false;
        }
    }
}
?>