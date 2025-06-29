<?php

if (!defined("WHMCS")) {
    die("Este arquivo não pode ser acessado diretamente.");
}

/**
 * Formatar valor como moeda
 */
function format_as_currency($amount) {
    return 'R$ ' . number_format($amount, 2, ',', '.');
}

/**
 * Buscar nome do departamento
 */
function get_department_name($department_id) {
    try {
        $result = select_query('tblticketdepartments', 'name', ['id' => $department_id]);
        $department = mysql_fetch_array($result);
        return $department ? $department['name'] : 'Departamento não encontrado';
    } catch (Exception $e) {
        logActivity("Erro ao buscar departamento: " . $e->getMessage());
        return 'Departamento não encontrado';
    }
}

/**
 * Validar número de telefone
 */
function validate_phone_number($phone) {
    // Remove todos os caracteres não numéricos
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Verifica se tem pelo menos 10 dígitos (formato mínimo brasileiro)
    if (strlen($phone) < 10) {
        return false;
    }
    
    // Se não começar com código do país, adiciona o código do Brasil (55)
    if (strlen($phone) == 10 || strlen($phone) == 11) {
        $phone = '55' . $phone;
    }
    
    return $phone;
}

/**
 * Limpar texto para WhatsApp
 */
function clean_whatsapp_text($text) {
    // Remove tags HTML
    $text = strip_tags($text);
    
    // Decodifica entidades HTML
    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
    
    // Remove quebras de linha excessivas
    $text = preg_replace('/\n{3,}/', "\n\n", $text);
    
    // Trim
    $text = trim($text);
    
    return $text;
}

/**
 * Gerar URL segura para visualização de fatura
 */
function generate_secure_invoice_url($invoice_id, $user_id) {
    try {
        // Busca dados da fatura
        $result = select_query('tblinvoices', 'id,hash', ['id' => $invoice_id, 'userid' => $user_id]);
        $invoice = mysql_fetch_array($result);
        
        if (!$invoice) {
            return '';
        }
        
        $system_url = rtrim(WHMCS\Config\Setting::getValue('SystemURL'), '/');
        
        // Se a fatura tem hash, usa URL com hash
        if (!empty($invoice['hash'])) {
            return $system_url . '/viewinvoice.php?id=' . $invoice['id'] . '&hash=' . $invoice['hash'];
        }
        
        // Senão, usa URL padrão
        return $system_url . '/viewinvoice.php?id=' . $invoice['id'];
        
    } catch (Exception $e) {
        logActivity("Erro ao gerar URL da fatura: " . $e->getMessage());
        return rtrim(WHMCS\Config\Setting::getValue('SystemURL'), '/') . '/viewinvoice.php?id=' . $invoice_id;
    }
}

/**
 * Gerar URL segura para visualização de ticket
 */
function generate_secure_ticket_url($ticket_id, $user_id) {
    try {
        // Busca dados do ticket
        $result = select_query('tbltickets', 'tid,c', ['id' => $ticket_id, 'userid' => $user_id]);
        $ticket = mysql_fetch_array($result);
        
        if (!$ticket) {
            return '';
        }
        
        $system_url = rtrim(WHMCS\Config\Setting::getValue('SystemURL'), '/');
        
        // URL com código de acesso
        return $system_url . '/viewticket.php?tid=' . $ticket['tid'] . '&c=' . $ticket['c'];
        
    } catch (Exception $e) {
        logActivity("Erro ao gerar URL do ticket: " . $e->getMessage());
        return rtrim(WHMCS\Config\Setting::getValue('SystemURL'), '/') . '/supporttickets.php';
    }
}

/**
 * Verificar se cliente tem WhatsApp válido
 */
function client_has_valid_whatsapp($user_id) {
    try {
        $result = select_query('tblclients', 'phonenumber', ['id' => $user_id]);
        $client = mysql_fetch_array($result);
        
        if (!$client || empty($client['phonenumber'])) {
            return false;
        }
        
        $phone = validate_phone_number($client['phonenumber']);
        return $phone !== false;
        
    } catch (Exception $e) {
        logActivity("Erro ao verificar WhatsApp do cliente: " . $e->getMessage());
        return false;
    }
}

/**
 * Obter informações do produto/serviço
 */
function get_service_info($service_id) {
    try {
        $result = select_query('tblhosting', '*', ['id' => $service_id]);
        $service = mysql_fetch_array($result);
        
        if (!$service) {
            return null;
        }
        
        // Busca informações do produto
        $product_result = select_query('tblproducts', '*', ['id' => $service['packageid']]);
        $product = mysql_fetch_array($product_result);
        
        return [
            'service' => $service,
            'product' => $product
        ];
        
    } catch (Exception $e) {
        logActivity("Erro ao buscar informações do serviço: " . $e->getMessage());
        return null;
    }
}

/**
 * Obter informações do domínio
 */
function get_domain_info($domain_id) {
    try {
        $result = select_query('tbldomains', '*', ['id' => $domain_id]);
        $domain = mysql_fetch_array($result);
        
        return $domain ? $domain : null;
        
    } catch (Exception $e) {
        logActivity("Erro ao buscar informações do domínio: " . $e->getMessage());
        return null;
    }
}

/**
 * Calcular dias até vencimento
 */
function days_until_due($due_date) {
    try {
        $due_timestamp = strtotime($due_date);
        $current_timestamp = time();
        
        $diff = $due_timestamp - $current_timestamp;
        $days = floor($diff / (60 * 60 * 24));
        
        return $days;
        
    } catch (Exception $e) {
        logActivity("Erro ao calcular dias até vencimento: " . $e->getMessage());
        return 0;
    }
}

/**
 * Verificar se é horário comercial
 */
function is_business_hours() {
    $current_hour = (int)date('H');
    $current_day = (int)date('N'); // 1 = Segunda, 7 = Domingo
    
    // Segunda a Sexta, das 8h às 18h
    if ($current_day >= 1 && $current_day <= 5) {
        return $current_hour >= 8 && $current_hour < 18;
    }
    
    // Sábado, das 8h às 12h
    if ($current_day == 6) {
        return $current_hour >= 8 && $current_hour < 12;
    }
    
    // Domingo - não é horário comercial
    return false;
}

/**
 * Obter configuração do módulo
 */
function get_module_setting($setting_name, $default_value = '') {
    try {
        $result = select_query('tbladdonmodules', 'value', [
            'module' => 'whatsapp_notification',
            'setting' => $setting_name
        ]);
        $setting = mysql_fetch_array($result);
        
        return $setting ? $setting['value'] : $default_value;
        
    } catch (Exception $e) {
        logActivity("Erro ao buscar configuração do módulo: " . $e->getMessage());
        return $default_value;
    }
}

/**
 * Salvar configuração do módulo
 */
function save_module_setting($setting_name, $value) {
    try {
        // Verifica se a configuração já existe
        $result = select_query('tbladdonmodules', 'id', [
            'module' => 'whatsapp_notification',
            'setting' => $setting_name
        ]);
        $existing = mysql_fetch_array($result);
        
        if ($existing) {
            // Atualiza
            return update_query('tbladdonmodules', 
                ['value' => $value],
                ['id' => $existing['id']]
            );
        } else {
            // Insere
            return insert_query('tbladdonmodules', [
                'module' => 'whatsapp_notification',
                'setting' => $setting_name,
                'value' => $value
            ]);
        }
        
    } catch (Exception $e) {
        logActivity("Erro ao salvar configuração do módulo: " . $e->getMessage());
        return false;
    }
}

/**
 * Truncar texto mantendo palavras completas
 */
function truncate_text($text, $max_length = 100) {
    if (strlen($text) <= $max_length) {
        return $text;
    }
    
    $truncated = substr($text, 0, $max_length);
    $last_space = strrpos($truncated, ' ');
    
    if ($last_space !== false) {
        $truncated = substr($truncated, 0, $last_space);
    }
    
    return $truncated . '...';
}

/**
 * Escapar texto para log
 */
function escape_for_log($text) {
    return addslashes(strip_tags($text));
}

/**
 * Verificar se string é JSON válido
 */
function is_valid_json($string) {
    json_decode($string);
    return json_last_error() === JSON_ERROR_NONE;
}

/**
 * Converter array para string legível
 */
function array_to_readable_string($array) {
    if (!is_array($array)) {
        return (string)$array;
    }
    
    $items = [];
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $items[] = $key . ': ' . array_to_readable_string($value);
        } else {
            $items[] = $key . ': ' . $value;
        }
    }
    
    return implode(', ', $items);
}
?>