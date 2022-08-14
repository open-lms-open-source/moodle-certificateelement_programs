<?php
// This file is part of the tool_certificate plugin for Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace certificateelement_programs;

/**
 * The certificate element for programs fields.
 *
 * @package    certificateelement_programs
 * @copyright  2022 Open LMS (https://www.openlms.net/)
 * @author     Petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class element extends \tool_certificate\element {

    /**
     * This function renders the form elements when adding a certificate element.
     *
     * @param \MoodleQuickForm $mform the edit_form instance
     */
    public function render_form_elements($mform) {

        // Get the program fields.
        $fields = self::get_program_fields();

        // Create the select box where the user field is selected.
        $mform->addElement('select', 'programfield', get_string('programfield', 'certificateelement_programs'), $fields);
        $mform->setType('programfield', PARAM_ALPHANUM);
        $mform->addHelpButton('programfield', 'programfield', 'certificateelement_programs');

        parent::render_form_elements($mform);
    }

    /**
     * Returns list of available program fields.
     *
     * @return array
     */
    protected static function get_program_fields(): array {
        return [
            'fullname' => get_string('programname', 'enrol_programs'),
            'idnumber' => get_string('programidnumber', 'enrol_programs'),
            'url' => get_string('programurl', 'enrol_programs'),
            'timecompleted' => get_string('programcompletion', 'enrol_programs'),
        ];
    }

    /**
     * Handles saving the form elements created by this element.
     * Can be overridden if more functionality is needed.
     *
     * @param \stdClass $data the form data or partial data to be updated
     */
    public function save_form_data(\stdClass $data) {
        $data->data = $data->programfield;
        parent::save_form_data($data);
    }

    /**
     * Handles rendering the element on the pdf.
     *
     * @param \pdf $pdf the pdf object
     * @param bool $preview true if it is a preview, false otherwise
     * @param \stdClass $user the user we are rendering this for
     * @param \stdClass $issue the issue we are rendering
     */
    public function render($pdf, $preview, $user, $issue) {
        $field = $this->get_data();

        if ($preview) {
            if ($field === 'fullname') {
                $value = 'Program 001';
                $value = format_string($value, true, ['context' => \context_system::instance()]);
            } else if ($field === 'idnumber') {
                $value = 'P001';
                $value = s($value);
            } else if ($field === 'url') {
                $url = new \moodle_url('/enrol/programs/catalogue/program', ['id' => 1]);
                $value = \html_writer::link($url, $url->out(false));
            } else if ($field === 'timecompleted') {
                $value = userdate(time());
            } else {
                $value = $field;
                $value = s($value);
            }
        } else {
            $data = (object)json_decode($issue->data);
            $value = get_string('error');
            if ($field === 'fullname') {
                if (isset($data->programfullname)) {
                    $value = $data->programfullname;
                    $value = format_string($value, true, ['context' => \context_system::instance()]);
                }
            } else if ($field === 'idnumber') {
                if (isset($data->programidnumber)) {
                    $value = $data->programidnumber;
                    $value = s($value);
                }
            } else if ($field === 'url') {
                if (isset($data->programid)) {
                    $url = new \moodle_url('/enrol/programs/catalogue/program', ['id' => $data->programid]);
                    $value = \html_writer::link($url, $url->out(false));
                }
            } else if ($field === 'timecompleted') {
                if (isset($data->programtimecompleted)) {
                    $value = userdate($data->programtimecompleted);
                }
            }
        }

        \tool_certificate\element_helper::render_content($pdf, $this, $value);
    }

    /**
     * Render the element in html.
     *
     * This function is used to render the element when we are using the
     * drag and drop interface to position it.
     */
    public function render_html() {
        // The value to display - we always want to show a value here so it can be repositioned.
        $fields = self::get_program_fields();
        $value = $fields[$this->get_data()] ?? $this->get_data();

        $value = format_string($value, true, ['context' => \context_system::instance()]);
        return \tool_certificate\element_helper::render_html_content($this, $value);
    }

    /**
     * Prepare data to pass to moodleform::set_data()
     *
     * @return \stdClass|array
     */
    public function prepare_data_for_form() {
        $record = parent::prepare_data_for_form();
        if ($this->get_data()) {
            $record->programfield = $this->get_data();
        }
        return $record;
    }
}
