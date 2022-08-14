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
 * Unit tests for programs element.
 *
 * @package    certificateelement_programs
 * @copyright  2022 Open LMS (https://www.openlms.net/)
 * @author     Petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class element_test extends \advanced_testcase {

    /**
     * Test set up.
     */
    public function setUp(): void {
        $this->resetAfterTest();
    }

    /**
     * Test render_html and pdf generator.
     */
    public function test_render_html() {
        global $CFG;

        /** @var \tool_certificate_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('tool_certificate');

        require_once($CFG->dirroot.'/user/profile/lib.php');

        $this->setAdminUser();

        $certificate1 = $generator->create_template((object)['name' => 'Certificate 1']);
        $pageid = $generator->create_page($certificate1)->get_id();

        $element = $generator->create_element($pageid, 'programs', ['programfield' => 'fullname']);
        $this->assertStringContainsString('Program name', $element->render_html());

        $formdata = (object)['name' => 'Program id', 'programfield' => 'idnumber'];
        $element = $generator->create_element($pageid, 'programs', $formdata);
        $this->assertStringContainsString('Program idnumber', $element->render_html());

        $element = $generator->create_element($pageid, 'programs', ['programfield' => 'url']);
        $this->assertStringContainsString('Program URL', $element->render_html());

        $element = $generator->create_element($pageid, 'programs', ['programfield' => 'timecompleted']);
        $this->assertStringContainsString('Program completion date', $element->render_html());

        // Generate PDF for preview.
        $filecontents = $generator->generate_pdf($certificate1, true);
        $filesize = \core_text::strlen($filecontents);
        $this->assertTrue($filesize > 30000 && $filesize < 90000);

        // Generate PDF for issue.
        $user = $this->getDataGenerator()->create_user();
        $issuedata = [
            'programid' => '1',
            'programfullname' => 'Program 001',
            'programidnumber' => 'P001',
            'programtimecompleted' => time(),
            'programallocationid' => '10',
        ];
        $issue = $generator->issue($certificate1, $user, null, $issuedata, 'enrol_programs');
        $filecontents = $generator->generate_pdf($certificate1, false, $issue);
        $filesize = \core_text::strlen($filecontents);
        $this->assertTrue($filesize > 30000 && $filesize < 90000);

        // Incorrectly manually generated cert.
        $issue = $generator->issue($certificate1, $user);
        $filecontents = $generator->generate_pdf($certificate1, false, $issue);
        $filesize = \core_text::strlen($filecontents);
        $this->assertTrue($filesize > 30000 && $filesize < 90000);
    }
}
