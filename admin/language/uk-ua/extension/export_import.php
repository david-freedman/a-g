<?php
// Heading
$_['heading_title']                         = '<strong style="color:#41637d">DEV-OPENCART.COM —</strong> Export / Import <a href="https://dev-opencart.com" target="_blank" title="Dev-opencart.com - Модули и шаблоны для Opencart"><img style="margin-left:15px;height:35px;margin-top:10px;margin-bottom:10px;" src="https://dev-opencart.com/logob.svg" alt="Dev-opencart.com - Модули и шаблоны для Opencart"/></a>';

// Text
$_['text_success'] = 'Успех: Вы успешно импортировали данные!';
$_['text_success_settings'] = 'Успех: Вы успешно обновили настройки для инструмента экспорта/импорта!';
$_['text_export_type_category'] = 'Категории (включая данные категории и фильтры)';
$_['text_export_type_category_old'] = 'Категории';
$_['text_export_type_product'] = 'Продукты (включая данные о продуктах, опции, спецпредложения, скидки, вознаграждения, атрибуты и фильтры)';
$_['text_export_type_product_old'] = 'Продукты (включая данные о продуктах, опции, специальные предложения, скидки, вознаграждения и атрибуты)';
$_['text_export_type_option'] = 'Определения опций';
$_['text_export_type_attribute'] = 'Определения атрибутов';
$_['text_export_type_filter'] = 'Определения фильтра';
$_['text_export_type_customer'] = 'Клиенты';
$_['text_yes'] = 'Да';
$_['text_no'] = 'Нет';
$_['text_nochange'] = 'Данные сервера не были изменены';
$_['text_log_details'] = 'См. также \'System &gt; Error Logs\' для более подробной информации.';
$_['text_log_details_2_0_x'] = 'См. также \'Tools &gt; Error Logs\' для более подробной информации.';
$_['text_log_details_2_1_x'] = 'См. также \'Система &gt; Инструменты &gt; Журналы ошибок\' для более подробной информации.';
$_['text_log_details_3_x'] = 'См. также <a href=«%1»>Система &gt; Обслуживание &gt; Журналы ошибок</a> для более подробной информации.';
$_['text_loading_notifications'] = 'Получение сообщений';
$_['text_retry'] = 'Повторная попытка';
$_['text_used_category_ids'] = 'Текущие идентификаторы используемых категорий находятся в диапазоне от %1 до %2.';
$_['text_used_product_ids'] = 'Используемые в данный момент идентификаторы товаров находятся между %1 и %2.';

// Ввод
$_['entry_import'] = 'Импорт из файла электронной таблицы XLS, XLSX или ODS';
$_['entry_export'] = 'Экспорт запрашиваемых данных в файл электронной таблицы XLSX';
$_['entry_export_type'] = 'Выберите, какие данные вы хотите экспортировать:';
$_['entry_range_type'] = 'Пожалуйста, выберите диапазон данных, которые вы хотите экспортировать:';
$_['entry_category_filter'] = 'Ограничить экспорт товаров по категориям:';
$_['entry_category'] = 'Категории';
$_['entry_start_id'] = 'Start id:';
$_['entry_start_index'] = 'Количество товаров в партии:';
$_['entry_end_id'] = 'End id:';
$_['entry_end_index'] = 'Номер партии:';
$_['entry_incremental'] = 'Использовать инкрементный импорт';
$_['entry_upload'] = 'File to be uploaded';
$_['entry_settings_use_option_id'] = 'Use <em>option_id</em> instead of <em>option name</em> in worksheets \'ProductOptions\' and \'ProductOptionValues\'';
$_['entry_settings_use_option_value_id'] = 'Use <em>option_value_id</em> instead of <em>option_value name</em> in worksheet \'ProductOptionValues\'';
$_['entry_settings_use_attribute_group_id'] = 'Use <em>attribute_group_id</em> instead of <em>attribute_group name</em> in worksheet \'ProductAttributes\'';
$_['entry_settings_use_attribute_id'] = 'Use <em>attribute_id</em> instead of <em>attribute name</em> in worksheet \'ProductAttributes\'';
$_['entry_settings_use_filter_group_id'] = 'Use <em>filter_group_id</em> instead of <em>filter_group name</em> in worksheets \'ProductFilters\' and \'CategoryFilters\'';
$_['entry_settings_use_filter_id'] = 'Use <em>filter_id</em> instead of <em>filter name</em> in worksheets \'ProductFilters\' and \'CategoryFilters\'';
$_['entry_settings_use_export_cache'] = 'Use phpTemp cache for large Exports (will be slightly slower)';
$_['entry_settings_use_import_cache'] = 'Use phpTemp cache for large Imports (will be slightly slower)';

// Error
$_['error_permission'] = 'Предупреждение: У вас нет прав на изменение экспорта/импорта!';
$_['error_upload'] = 'В загруженном файле электронной таблицы есть ошибки проверки!';
$_['error_worksheets'] = 'Экспорт/Импорт: Недопустимые имена рабочих листов';
$_['error_categories_header'] = 'Экспорт/Импорт: Неверный заголовок в рабочем листе «Категории';
$_['error_category_filters_header'] = 'Экспорт/Импорт: Неверный заголовок в рабочем листе CategoryFilters';
$_['error_category_seo_keywords_header'] = 'Экспорт/Импорт: Неверный заголовок в рабочем листе CategorySEOKeywords';
$_['error_products_header'] = 'Экспорт/Импорт: Неверный заголовок в рабочей таблице Products';
$_['error_additional_images_header'] = 'Экспорт/Импорт: Неверный заголовок в рабочем листе AdditionalImages';
$_['error_specials_header'] = 'Экспорт/Импорт: Неверный заголовок в рабочем листе Specials';

$_['error_discounts_header'] = 'Экспорт/Импорт: Неверный заголовок в рабочем листе «Скидки';
$_['error_rewards_header'] = 'Экспорт/Импорт: Неверный заголовок в рабочем листе Вознаграждения';
$_['error_product_options_header'] = 'Экспорт/Импорт: Неверный заголовок в рабочем листе ProductOptions';
$_['error_product_option_values_header'] = 'Экспорт/Импорт: Неверный заголовок в рабочем листе ProductOptionValues';
$_['error_product_attributes_header'] = 'Экспорт/Импорт: Неверный заголовок в рабочем листе ProductAttributes';
$_['error_product_filters_header'] = 'Экспорт/Импорт: Неверный заголовок в рабочем листе ProductFilters';
$_['error_product_seo_keywords_header'] = 'Экспорт/Импорт: Неверный заголовок в рабочем листе ProductSEOKeywords';
$_['error_options_header'] = 'Экспорт/Импорт: Неверный заголовок в рабочем листе «Параметры';
$_['error_option_values_header'] = 'Экспорт/Импорт: Неверный заголовок в рабочем листе OptionValues';
$_['error_attribute_groups_header'] = 'Экспорт/Импорт: Неверный заголовок в рабочем листе AttributeGroups';
$_['error_attributes_header'] = 'Экспорт/Импорт: Неверный заголовок в рабочем листе «Атрибуты';
$_['error_filter_groups_header'] = 'Экспорт/Импорт: Неверный заголовок в рабочем листе FilterGroups';
$_['error_filters_header'] = 'Экспорт/Импорт: Неверный заголовок в рабочем листе «Фильтры';
$_['error_customers_header'] = 'Экспорт/Импорт: Неверный заголовок в рабочем листе «Клиенты';
$_['error_addresses_header'] = 'Экспорт/Импорт: Неверный заголовок в рабочем листе «Адреса';
$_['error_product_options'] = 'Экспорт/Импорт: Отсутствует рабочий лист Products, или рабочий лист Products не указан перед ProductOptions';
$_['error_product_option_values'] = 'Экспорт/Импорт: Отсутствует рабочий лист продуктов, или рабочий лист продуктов не указан перед ProductOptionValues';
$_['error_product_option_values_2'] = 'Экспорт/Импорт: Отсутствует рабочий лист ProductOptions, или рабочий лист ProductOptions не указан перед ProductOptionValues';
$_['error_product_option_values_3'] = 'Экспорт/Импорт: Рабочий лист ProductOptionValues также ожидается после рабочего листа ProductOptions';
$_['error_additional_images'] = 'Экспорт/Импорт: Отсутствует рабочий лист Products, или рабочий лист Products не указан перед AdditionalImages';
$_['error_specials'] = 'Экспорт/Импорт: Отсутствует рабочий лист «Продукты», или рабочий лист «Продукты» не указан перед «Особыми';
$_['error_discounts'] = 'Экспорт/Импорт: Отсутствует рабочий лист «Продукты», или рабочий лист «Продукты» не указан перед «Скидками';
$_['error_rewards'] = 'Экспорт/Импорт: Отсутствует рабочий лист «Продукты», или рабочий лист «Продукты» не указан перед «Вознаграждениями';
$_['error_product_attributes'] = 'Экспорт/Импорт: Отсутствует рабочий лист «Продукты», или рабочий лист «Продукты» не указан перед «ProductAttributes';
$_['error_attributes'] = 'Экспорт/Импорт: Отсутствует рабочий лист AttributeGroups, или рабочий лист AttributeGroups не указан перед Attributes';
$_['error_attributes_2'] = 'Экспорт/Импорт: Рабочий лист Attributes также ожидается после рабочего листа AttributeGroups';
$_['error_category_filters'] = 'Экспорт/Импорт: Отсутствует рабочий лист Categories, или рабочий лист Categories не указан перед CategoryFilters';
$_['error_category_seo_keywords'] = 'Экспорт/Импорт: Отсутствует рабочий лист Categories, или рабочий лист Categories не указан перед CategorySEOKeywords';
$_['error_product_filters'] = 'Экспорт/Импорт: Отсутствует рабочий лист Products, или рабочий лист Products не указан перед ProductFilters';
$_['error_product_seo_keywords'] = 'Экспорт/Импорт: Отсутствует рабочий лист «Продукты», или рабочий лист «Продукты» не указан перед ProductSEOKeywords';
$_['error_filters'] = 'Экспорт/Импорт: Отсутствует рабочий лист FilterGroups, или рабочий лист FilterGroups не указан перед Filters';
$_['error_filters_2'] = 'Экспорт/Импорт: Рабочий лист Filters также ожидается после рабочего листа FilterGroups';
$_['error_option_values'] = 'Экспорт/Импорт: Отсутствует рабочий лист Options, или рабочий лист Options не указан перед OptionValues';
$_['error_option_values_2'] = 'Экспорт/Импорт: Рабочий лист OptionValues также ожидается после рабочего листа Options';
$_['error_post_max_size'] = 'Размер файла превышает %1 (см. настройку PHP \'post_max_size\')';
$_['error_upload_max_filesize'] = 'Размер файла больше %1 (см. настройку PHP \'upload_max_filesize\')';
$_['error_select_file'] = 'Пожалуйста, выберите файл перед нажатием кнопки \'Import\'';
$_['error_id_no_data'] = 'Нет данных между start-id и end-id.';
$_['error_page_no_data'] = 'Больше нет данных';

$_['error_param_not_number'] = 'Values for data range must be whole numbers.';
$_['error_upload_name'] = 'Missing file name for upload';
$_['error_upload_ext'] = 'Uploaded file has not one of the \'.xls\', \'.xlsx\' or \'.ods\' file name extensions, it might not be a spreadsheet file!';
$_['error_notifications'] = 'Could not load messages from MHCCORP.COM.';
$_['error_no_news'] = 'No messages';
$_['error_batch_number'] = 'Batch number must be greater than 0';
$_['error_min_item_id'] = 'Start id must be greater than 0';
$_['error_option_name'] = 'Option \'%1\' is defined multiple times!<br />';
$_['error_option_name'] = 'In the Settings-tab please activate the following:<br />';
$_['error_option_name'] = 'Use <em>option_id</em> instead of <em>option name</em> in worksheets „ProductOptions“ and „ProductOptionValues“';
$_['error_option_value_name'] = 'Option value \'%1\' is defined multiple times within its option!<br />';
$_['error_option_value_name'] = 'In the Settings-tab please activate the following:<br />';
$_['error_option_value_name'] = 'Use <em>option_value_id</em> instead of <em>option_value name</em> in worksheet „ProductOptionValues“';
$_['error_attribute_group_name'] = 'AttributeGroup \'%1\' is defined multiple times!<br />';
$_['error_attribute_group_name'] = 'In the Settings-tab please activate the following:<br />';
$_['error_attribute_group_name'] = 'Use <em>attribute_group_id</em> instead of <em>attribute_group name</em> in worksheets „ProductAttributes“';
$_['error_attribute_name'] = 'Attribute \'%1\' is defined multiple times within its attribute group!<br />';
$_['error_attribute_name'] = 'In the Settings-tab please activate the following:<br />';
$_['error_attribute_name'] = 'Use <em>attribute_id</em> instead of <em>attribute name</em> in worksheet „ProductAttributes“';
$_['error_filter_group_name'] = 'FilterGroup \'%1\' is defined multiple times!<br />';
$_['error_filter_group_name'] = 'In the Settings-tab please activate the following:<br />';
$_['error_filter_group_name'] = 'Use <em>filter_group_id</em> instead of <em>filter_group name</em> in worksheets „ProductFilters“';
$_['error_filter_name'] = 'Filter \'%1\' is defined multiple times within its filter group!<br />';
$_['error_filter_name'] = 'In the Settings-tab please activate the following:<br />';
$_['error_filter_name'] = 'Use <em>filter_id</em> instead of <em>filter name</em> in worksheet „ProductFilters“';
$_['error_incremental'] = 'Missing „incremental“ (Yes or No) selection for Import';

$_['error_missing_customer_group'] = 'Export/Import: Missing customer_groups in worksheet \'%1\'!';
$_['error_invalid_customer_group'] = 'Export/Import: Undefined customer_group \'%2\' used in worksheet \'%1\'!';
$_['error_missing_product_id'] = 'Export/Import: Missing product_ids in worksheet \'%1\'!';
$_['error_missing_option_id'] = 'Export/Import: Missing option_ids in worksheet \'%1\'!';
$_['error_invalid_option_id'] = 'Export/Import: Undefined option_id \'%2\' used in worksheet \'%1\'!';
$_['error_missing_option_name'] = 'Export/Import: Missing option_names in worksheet \'%1\'!';
$_['error_invalid_product_id_option_id'] = 'Export/Import: Option_id \'%3\' not specified for product_id \'%2\' in worksheet \'%4\', but it is used in worksheet \'%1\'!';
$_['error_missing_option_value_id'] = 'Экспорт/Импорт: Недостающие идентификаторы опций_значений в рабочем листе \'%1\'!';
$_['error_invalid_option_id_option_value_id'] = 'Экспорт/Импорт: Неопределенный опционный_значение_ид \'%3\' для опционного_ида \'%2\', используемого в рабочем листе \'%1\'!';
$_['error_missing_option_value_name'] = 'Экспорт/Импорт: Недостающие имена_значений_опций в рабочем листе \'%1\'!';
$_['error_invalid_option_id_option_value_name'] = 'Экспорт/Импорт: Неопределенное имя_значения_опции \'%3\' для идентификатора опции \'%2\', используемого в рабочем листе \'%1\'!'; 
$_['error_invalid_option_name'] = 'Экспорт/Импорт: Неопределенное имя_опции \'%2\' используется в рабочем листе \'%1\'!';
$_['error_invalid_product_id_option_name'] = 'Экспорт/Импорт: Имя_опции \'%3\' не указано для product_id \'%2\' в рабочем листе \'%4\', но используется в рабочем листе \'%1\'!';
$_['error_invalid_option_name_option_value_id'] = 'Экспорт/Импорт: Неопределенный идентификатор_значения_опции \'%3\' для имени_опции \'%2\', используемого в рабочем листе \'%1\'!';

$_['error_invalid_option_name_option_value_name'] = 'Экспорт/Импорт: Неопределенное имя_значения_опции \'%3\' для имени_опции \'%2\', используемого в рабочем листе \'%1\'!';
$_['error_missing_attribute_group_id'] = 'Экспорт/Импорт: Отсутствует идентификатор группы_атрибутов в рабочем листе \'%1\'!';
$_['error_invalid_attribute_group_id'] = 'Экспорт/Импорт: Неопределенный идентификатор группы атрибутов \'%2\' использован в рабочем листе \'%1\'!';
$_['error_missing_attribute_group_name'] = 'Экспорт/Импорт: Недостающие имена_групп_атрибутов в рабочем листе \'%1\'!';
$_['error_missing_attribute_id'] = 'Экспорт/Импорт: Недостающие идентификаторы атрибутов в рабочем листе \'%1\'!';
$_['error_invalid_attribute_group_id_attribute_id'] = 'Экспорт/Импорт: Неопределенный attribute_id \'%3\' для attribute_group_id \'%2\', используемый в рабочем листе \'%1\'!';
$_['error_missing_attribute_name'] = 'Экспорт/Импорт: Недостающие имена_атрибутов в рабочем листе \'%1\'!';
$_['error_invalid_attribute_group_id_attribute_name'] = 'Экспорт/Импорт: Неопределенное имя_атрибута \'%3\' для идентификатора опции \'%2\', используемого в рабочем листе \'%1\'!'; 
$_['error_invalid_attribute_group_name'] = 'Экспорт/Импорт: Неопределенное имя_группы_атрибутов \'%2\' используется в рабочем листе \'%1\'!';
$_['error_invalid_attribute_group_name_attribute_id'] = 'Экспорт/Импорт: Неопределенный attribute_id \'%3\' для attribute_group_name \'%2\', используемый в рабочем листе \'%1\'!';
$_['error_invalid_attribute_group_name_attribute_name'] = 'Экспорт/Импорт: Неопределенное имя_атрибута \'%3\' для имени_группы_атрибутов \'%2\', используемое в рабочем листе \'%1\'!';
$_['error_missing_filter_group_id'] = 'Экспорт/Импорт: Отсутствует идентификатор filter_group_ids в рабочем листе \'%1\'!';
$_['error_invalid_filter_group_id'] = 'Экспорт/Импорт: Неопределенный идентификатор filter_group_id \'%2\' используется в рабочем листе \'%1\'!';
$_['error_missing_filter_group_name'] = 'Экспорт/Импорт: Отсутствуют имена_групп_фильтров в рабочем листе \'%1\'!';
$_['error_missing_filter_id'] = 'Экспорт/Импорт: Отсутствует идентификатор_фильтра в рабочем листе \'%1\'!';
$_['error_invalid_filter_group_id_filter_id'] = 'Экспорт/Импорт: Неопределенный filter_id \'%3\' для filter_group_id \'%2\', используемый в рабочем листе \'%1\'!';
$_['error_missing_filter_name'] = 'Экспорт/Импорт: Отсутствует имя_фильтра в рабочем листе \'%1\'!';
$_['error_invalid_filter_group_id_filter_name'] = 'Экспорт/Импорт: Неопределенное имя_фильтра \'%3\' для идентификатора опции \'%2\', используемого в рабочем листе \'%1\'!'; 
$_['error_invalid_filter_group_name'] = 'Экспорт/Импорт: Неопределенное имя_группы_фильтров \'%2\' используется в рабочем листе \'%1\'!';
$_['error_invalid_filter_group_name_filter_id'] = 'Экспорт/Импорт: Неопределенный идентификатор фильтра \'%3\' для имени_группы_фильтра \'%2\', используемого в рабочем листе \'%1\'!';
$_['error_invalid_filter_group_name_filter_name'] = 'Экспорт/Импорт: Неопределенное имя_фильтра \'%3\' для имени_группы_фильтров \'%2\', используемого в рабочем листе \'%1\'!';
$_['error_invalid_product_id'] = 'Экспорт/Импорт: Неверный идентификатор продукта \'%2\' использован в рабочей таблице \'%1\'!';
$_['error_duplicate_product_id'] = 'Экспорт/Импорт: Дублированный идентификатор товара \'%2\' использован в рабочей таблице \'%1\'!';
$_['error_unlisted_product_id'] = 'Экспорт/Импорт: Рабочая таблица \'%1\' не может использовать идентификатор продукта \'%2\', потому что он не указан в рабочей таблице \'Продукты\'!';
$_['error_wrong_order_product_id'] = 'Экспорт/Импорт: В рабочем листе \'%1\' используется идентификатор продукта \'%2\' в неправильном порядке. Ожидается восходящий порядок!';
$_['error_filter_not_supported'] = 'Экспорт/Импорт: Фильтры не поддерживаются в вашей версии OpenCart!';
$_['error_seo_keywords_not_supported'] = 'Экспорт/Импорт: Рабочая таблица \'%1\' не поддерживается в вашей версии OpenCart!';
$_['error_missing_category_id'] = 'Экспорт/Импорт: Отсутствуют идентификаторы категорий в рабочей таблице \'%1\'!';
$_['error_invalid_category_id'] = 'Экспорт/Импорт: Неверный идентификатор категории \'%2\' использован в рабочем листе \'%1\'!';
$_['error_duplicate_category_id'] = 'Экспорт/Импорт: Дублированный идентификатор категории \'%2\' использован в рабочей таблице \'%1\'!';
$_['error_wrong_order_category_id'] = 'Экспорт/Импорт: В рабочем листе \'%1\' используется идентификатор категории \'%2\' в неправильном порядке. Ожидается восходящий порядок!';
$_['error_unlisted_category_id'] = 'Экспорт/Импорт: Рабочий лист \'%1\' не может использовать идентификатор категории \'%2\', потому что он не указан в рабочем листе \'Categories\'!';
$_['error_addresses'] = 'Экспорт/Импорт: Отсутствует рабочий лист Cutomers, или рабочий лист Customers не указан перед Addresses!';
$_['error_addresses_2'] = 'Экспорт/Импорт: Рабочий лист «Адреса» также ожидается после рабочего листа «Клиенты»';

$_['error_invalid_store_id'] = 'Экспорт/Импорт: Неверный store_id=\'%1\' используется в рабочем листе \'%2\'!';
$_['error_missing_customer_id'] = 'Экспорт/Импорт: Отсутствует идентификатор клиента в рабочей таблице \'%1\'!';
$_['error_invalid_customer_id'] = 'Экспорт/Импорт: Неверный идентификатор клиента \'%2\' использован в рабочей таблице \'%1\'!';
$_['error_duplicate_customer_id'] = 'Экспорт/Импорт: Дублированный идентификатор клиента \'%2\' использован в рабочей таблице \'%1\'!';
$_['error_wrong_order_customer_id'] = 'Экспорт/Импорт: Рабочая таблица \'%1\' использует идентификатор клиента \'%2\' в неправильном порядке. Ожидается восходящий порядок!';
$_['error_unlisted_customer_id'] = 'Экспорт/Импорт: Рабочая таблица \'%1\' не может использовать идентификатор клиента \'%2\', потому что он не указан в рабочей таблице \'Customers\'!';
$_['error_missing_country_col'] = 'Экспорт/Импорт: В рабочем листе \'%1\' отсутствует заголовок столбца \'страна\'!';
$_['error_missing_zone_col'] = 'Экспорт/Импорт: В рабочем листе \'%1\' отсутствует заголовок столбца \'зона\'!';
$_['error_undefined_country'] = 'Экспорт/Импорт: Неопределенная страна \'%1\' используется в рабочем листе \'%2\'!';
$_['error_undefined_zone'] = 'Экспорт/Импорт: Неопределенная зона \'%2\' для страны \'%1\', используемая в рабочей таблице \'%3\'!';
$_['error_incremental_only'] = 'Экспорт/Импорт: Рабочий лист \'%1\' может быть импортирован только в инкрементальном режиме!';
$_['error_multiple_category_id_store_id'] = 'Экспорт/Импорт: Дублируется идентификатор категории/магазина \'%1\'/\'%2\' в рабочей таблице \'CategorySEOKeywords\'!';
$_['error_multiple_product_id_store_id'] = 'Экспорт/Импорт: В рабочей таблице \'%1\'/\'%2\' указан дубликат product_id/store_id \'%1\'/\'%2\'!';
$_['error_unique_keyword'] = 'Экспорт/Импорт: Ключевое слово \'%1\' используется более одного раза для идентификатора магазина \'%2\' в рабочей таблице \'%3\'!';

// Вкладки
$_['tab_import'] = 'Импорт';
$_['tab_export'] = 'Экспорт';
$_['tab_settings'] = 'Настройки';

// Ярлыки кнопок
$_['button_import'] = 'Импорт';
$_['button_export'] = 'Экспорт';
$_['button_settings'] = 'Обновить настройки';
$_['button_export_id'] = 'По диапазону id';
$_['button_export_page'] = 'По партиям';

// Помощь
$_['help_range_type'] = '(Необязательно, оставьте пустым, если не нужно)';
$_['help_category_filter'] = '(Необязательно, оставьте пустым, если не нужно)';
$_['help_incremental_yes'] = '(Обновление и/или добавление данных)';
$_['help_incremental_no'] = '(Удалить все старые данные перед импортом)';
$_['help_import'] = 'Электронная таблица может содержать категории, товары, определения атрибутов, определения опций или определения фильтров. ';
$_['help_import_old'] = 'Электронная таблица может содержать категории, продукты, определения атрибутов или опций. ';
$_['help_format'] = 'Сначала сделайте экспорт, чтобы увидеть точный формат рабочих листов!';
?>