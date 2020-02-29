<?php
// This file is part of Moodle - http://moodle.org/
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

/**
 * This file contains the scheduled tasks needed by the plugin.
 *
 * @package     cachestore_download_optimizer
 * @copyright   2020 Gustavo Mejía <bfmvtm@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace cachestore_download_optimizer\task;
include_once($CFG->dirroot.'/cache/stores/download_optimizer/lib.php');
 
/**
 * Scheduled task to request new recommendations and send metrics.
 */
class http_requests extends \core\task\scheduled_task {
 
    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('httprequests', 'cachestore_download_optimizer');
    }
 
    /**
     * Execute the task.
     */
    public function execute() {
        send_metrics();

        $recommendations = get_recommendations();

        clear_cache($recommendations);
        retrieve_files($recommendations);
    }
}
