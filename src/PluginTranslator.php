<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class PluginTranslator implements Translator
{
    /**
     * @readonly
     */
    private \ilPluginLanguage $language_handler;
    private bool $auto_language_update = false;

    public function __construct(\ilPluginLanguage $language_handler)
    {
        $this->language_handler = $language_handler;
        if ($this->auto_language_update) {
            // sort language file entries
            $en_lang = __DIR__ . "/../lang/ilias_de.lang";
            $current_content = file_get_contents($en_lang);
            $lines = explode("\n", $current_content);
            sort($lines);
            $lines = array_filter($lines, fn ($line): bool => trim($line) !== '' && trim($line) !== '0');
            file_put_contents($en_lang, implode("\n", $lines) . "\n");

            $this->language_handler->updateLanguages();
        }
    }

    public function txt(string $a_var, ?string $module = null): string
    {
        $language_variable = ($module === null ? '' : $module . '_') . $a_var;
        if ($this->auto_language_update) {
            $en_lang = __DIR__ . "/../lang/ilias_de.lang";

            $current_content = file_get_contents($en_lang);

            if (!preg_match('#^' . $language_variable . '\#:\#.*#m', $current_content)) {
                file_put_contents($en_lang, $language_variable . "#:#" . $language_variable . "\n", FILE_APPEND);
            }
        }

        return $this->language_handler->txt($language_variable);
    }
}
