<?php
namespace WJM\Application\Controllers;

use WJM\Domain\Repositories\FormMessageRepositoryInterface;
use WJM\Infra\WordPress\View;

class FormMessageController
{
    private View $view;
    
    public function __construct(private FormMessageRepositoryInterface $repo, View $view) 
    {
        $this->view = $view;
    }

    public function list(): void
    {
        $search = sanitize_text_field($_GET['s'] ?? '');
        $page = max(1, (int)($_GET['paged'] ?? 1));
        $perPage = 20; // Aumente para melhor usabilidade

        $messages = $this->repo->getMessages($search, $page, $perPage);
        $total = $this->repo->countMessages($search);

        $this->view->render('admin/messages/list', [
            'messages' => $messages,
            'total' => $total,
            'currentPage' => $page,
            'perPage' => $perPage,
            'searchTerm' => $search
        ]);
    }

    public function show(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('Você não tem permissão para acessar esta página.'), 403);
        }

        $messageId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($messageId <= 0) {
            wp_die(__('ID da mensagem inválido.'), 400);
        }

        $message = $this->repo->findById($messageId);
        
        if (!$message) {
            wp_die(__('Mensagem não encontrada.'), 404);
        }

        $currentUser = wp_get_current_user();
        $this->repo->markAsViewed($messageId, $currentUser->user_login);

        $this->view->render('admin/messages/show', [
            'message' => $message,
            'back_url' => admin_url('admin.php?page=wjm_form_messages')
        ]);
    }
    public function getMessageDetails(): void
    {
        check_ajax_referer('wjm_message_nonce', 'nonce');

        $messageId = (int) ($_GET['id'] ?? 0);
        $message = $this->repo->findById($messageId);

        if (!$message) {
            wp_send_json_error(['message' => 'Mensagem não encontrada']);
        }

        $html = '<h2>Detalhes da Mensagem #' . $message->getId() . '</h2>';
        $html .= '<div class="message-details">';
        $html .= '<p><strong>Formulário:</strong> ' . esc_html($message->getFormTitle()) . '</p>';
        $html .= '<p><strong>Data:</strong> ' . esc_html($message->getSubmittedAt()) . '</p>';
        $html .= '<p><strong>IP:</strong> ' . esc_html($message->ipAddress) . '</p>';
        
        $html .= '<h3>Dados:</h3><ul>';
        foreach ($message->getData() as $key => $value) {
            $html .= '<li><strong>' . esc_html(ucfirst($key)) . ':</strong> ' . esc_html($value) . '</li>';
        }
        $html .= '</ul></div>';

        wp_send_json_success(['html' => $html]);
    }
}
