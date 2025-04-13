<?php

use app\models\Session;

global $SITE_URL;
$page = $data['page'] ?? null;
$title = "Предпросмотр: " . ($page ? htmlspecialchars($page->title) : 'Страница');

if (!$page) {
    return Router::redirect_to('/admin/pages');
}

$header_actions = [
    [
        'label' => 'Редактировать страницу',
        'url' => Router::getRoute('/admin/pages/' . $page->id . '/edit'),
        'class' => 'btn-primary',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>'
    ],
    [
        'label' => 'Вернуться к деталям',
        'url' => Router::getRoute('/admin/pages/' . $page->id),
        'class' => 'btn-outline-secondary',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>'
    ]
];

ob_start();
?>

<div class="page-preview">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Предпросмотр: <?= htmlspecialchars($page->title) ?></h2>
            <div class="device-switcher">
                <button class="device-button active" data-device="desktop" title="Версия для компьютера">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                        <line x1="8" y1="21" x2="16" y2="21"></line>
                        <line x1="12" y1="17" x2="12" y2="21"></line>
                    </svg>
                </button>
                <button class="device-button" data-device="tablet" title="Версия для планшета">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect>
                        <line x1="12" y1="18" x2="12.01" y2="18"></line>
                    </svg>
                </button>
                <button class="device-button" data-device="mobile" title="Мобильная версия">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
                        <line x1="12" y1="18" x2="12.01" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>

        <div class="preview-status-bar">
            <div class="status-item">
                <span class="status-label">Статус:</span>
                <span class="status-value <?= $page->is_published ? 'published' : 'draft' ?>">
                    <?= $page->is_published ? 'Опубликовано' : 'Черновик' ?>
                </span>
            </div>
            <div class="status-item">
                <span class="status-label">URL:</span>
                <a href="<?= Router::getRoute('/page/' . $page->slug) ?>" class="status-link" target="_blank">
                    /page/<?= htmlspecialchars($page->slug) ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                        <polyline points="15 3 21 3 21 9"></polyline>
                        <line x1="10" y1="14" x2="21" y2="3"></line>
                    </svg>
                </a>
            </div>
            <div class="current-device">
                <span class="device-label">Устройство:</span>
                <span class="device-name">Компьютер</span>
            </div>
        </div>

        <div class="preview-container">
            <div class="preview-frame-wrapper" data-current-device="desktop">
                <iframe id="preview-frame" src="<?= Router::getRoute('/page/' . $page->slug . '/preview') ?>" frameborder="0"></iframe>
            </div>
        </div>

        <div class="preview-footer">
            <div class="alert alert-info mb-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                <span>Это предпросмотр. После публикации страница может выглядеть иначе.</span>
            </div>
        </div>
    </div>
</div>

<style>
    /* Стили не нуждаются в переводе */
    .page-preview .card {
        overflow: hidden;
    }

    .page-preview .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .device-switcher {
        display: flex;
        gap: 10px;
    }

    .device-button {
        background-color: var(--admin-gray-100);
        border: 1px solid var(--admin-gray-300);
        border-radius: var(--admin-border-radius);
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all var(--admin-transition-speed);
    }

    .device-button:hover {
        background-color: var(--admin-gray-200);
    }

    .device-button.active {
        background-color: var(--admin-primary-color);
        border-color: var(--admin-primary-color);
        color: white;
    }

    .preview-status-bar {
        background-color: var(--admin-gray-100);
        padding: 10px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid var(--admin-gray-300);
        border-bottom: 1px solid var(--admin-gray-300);
    }

    .status-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .status-label {
        color: var(--admin-gray-600);
        font-weight: 500;
    }

    .status-value {
        font-weight: 600;
    }

    .status-value.published {
        color: var(--admin-success-color);
    }

    .status-value.draft {
        color: var(--admin-warning-color);
    }

    .status-link {
        display: flex;
        align-items: center;
        gap: 5px;
        color: var(--admin-primary-color);
    }

    .current-device {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .device-name {
        font-weight: 600;
        color: var(--admin-primary-color);
    }

    .preview-container {
        background-color: var(--admin-gray-200);
        min-height: 500px;
        position: relative;
        padding: 30px;
        display: flex;
        justify-content: center;
    }

    .preview-frame-wrapper {
        background-color: white;
        border-radius: var(--admin-border-radius);
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        width: 100%;
        height: 600px;
    }

    .preview-frame-wrapper[data-current-device="desktop"] {
        width: 100%;
    }

    .preview-frame-wrapper[data-current-device="tablet"] {
        width: 768px;
        border: 12px solid var(--admin-gray-800);
        border-radius: 12px;
    }

    .preview-frame-wrapper[data-current-device="mobile"] {
        width: 375px;
        border: 10px solid var(--admin-gray-800);
        border-radius: 25px;
    }

    #preview-frame {
        width: 100%;
        height: 100%;
        background-color: white;
    }

    .preview-footer {
        padding: 15px;
    }

    .preview-footer .alert {
        display: flex;
        align-items: center;
        gap: 10px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Функциональность переключения устройства
        const deviceButtons = document.querySelectorAll('.device-button');
        const previewWrapper = document.querySelector('.preview-frame-wrapper');
        const deviceNameDisplay = document.querySelector('.device-name');

        deviceButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Удаляем активный класс со всех кнопок
                deviceButtons.forEach(btn => btn.classList.remove('active'));

                // Добавляем активный класс нажатой кнопке
                this.classList.add('active');

                // Получаем тип устройства
                const device = this.getAttribute('data-device');

                // Обновляем обертку фрейма предпросмотра
                previewWrapper.setAttribute('data-current-device', device);

                // Обновляем отображение названия устройства
                const deviceNames = {
                    'desktop': 'Компьютер',
                    'tablet': 'Планшет',
                    'mobile': 'Телефон'
                };
                deviceNameDisplay.textContent = deviceNames[device] || device;
            });
        });

        // Проверяем загрузку содержимого iframe
        const iframe = document.getElementById('preview-frame');
        iframe.onload = function() {
            // Дополнительно: можно добавить поведение после загрузки iframe
        };
    });
</script>

<?php
$content = ob_get_clean();
include('views/admin/layout.php');
?>