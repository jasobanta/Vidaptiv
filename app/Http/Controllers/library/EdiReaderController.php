<?php

namespace App\Http\Controllers\library;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EdiReaderController extends Controller {

    /**
     * Reader constructor.
     *
     * @param string $url url or path ur EDI message
     */
    public function __construct(string $url = null) {
        if (isset($url)) {
            $this->load($url);
        }
    }

    /**
     * @param string $url url to edi file, path to edi file or EDI message
     *
     * @return bool
     */
    public function load(string $url) {
        try {
            $contents = [];
            if (Storage::disk('local')->exists($url)) {
                $contents = Storage::disk('local')->get($url);
                $contents = $this->fileLineBreak($contents);
            }
            $this->line_segments = $contents;
            $this->fileExtractor();
        } catch (\Exception $e) {
            Log::info("SERVER_ERROR: load(): " . $e->getMessage());
        }
    }

    public function getParsedFile() {
        $segment_data = [];
        try {
            if (!empty($this->line_segments)) {
                foreach ($this->line_segments as $segments) {
                    $segment_data[] = explode("+", $this->colonSeparator($segments));
                }
            }
        } catch (\Exception $e) {
            Log::info("SERVER_ERROR: getParsedFile(): " . $e->getMessage());
        }
        return $this->segment_data = $segment_data;
    }

    public function fileExtractor() { //need to check this function is usable or not
        $segment_data = [];
        try {
            if (!empty($this->line_segments)) {
                foreach ($this->line_segments as $segments) {
                    $segment_data[] = explode("+", $segments);
                }
            }
        } catch (\Exception $e) {
            Log::info("SERVER_ERROR: fileExtractor(): " . $e->getMessage());
        }
        return $this->segment_data = $segment_data;
    }

    public function segmentBreak($str = '', $break_sign = "+") {
        $str = str_replace("?+", "#ignore#plus#", $str);
        $str = str_replace("?:", "#ignore#colon#", $str);
        $str = explode($break_sign, $str);
        $str = str_replace("#ignore#plus#", "?+", $str);
        return $str = str_replace("#ignore#colon#", "?:", $str);
    }

    public function readEdiDataValue($filters = '') {
        $data = [];
        try {
            if (!empty($this->line_segments)) {
                foreach ($this->line_segments as $all_segments) {
                    if (str_starts_with($all_segments, $filters)) {
                        $segments = explode($filters, $all_segments);
                        $segment_str = isset($segments[1]) ? $segments[1] : '';
                        $first_char = substr($segment_str, 0, 1);

                        if ($segment_str != '' && ( $first_char == "+" || $first_char == ":")) {
                            $rules = explode("+", $filters);
                            $sub_segments = str_replace($rules[0], '', $all_segments);

                            //$segment_str = ltrim($segment_str, '+'); //remove extra + start from string
                            $check_first_plus = str_starts_with($sub_segments, '+');
                            if ($check_first_plus) {
                                $segments_line = substr($sub_segments, 1);
                            } else {
                                $segments_line = $sub_segments;
                            }
                            $line_array = [];
                            $line_data = $this->segmentBreak($segments_line, "+");
                            if (!empty($line_data)) {
                                foreach ($line_data as $line) {
                                    $line_array[] = $this->segmentBreak($this->excludeSegment($filters, $line), ":");
                                }
                            }
                            $data[] = $line_array;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::info("SERVER_ERROR: readEdiDataValue(): " . $e->getMessage());
        }
        return $data;
    }

    public function excludeSegment($filters, $data) {
        if ($filters == "COM") {
            $data = str_replace([":TE", ":EM", ":FX"], "", $data);
        }
        return $data;
    }

    public function getFirstStrValue($rule = '', $key = 1) {
        try {
            if (!empty($this->readEdiDataValue($rule))) {
                return collect($this->readEdiDataValue($rule))->flatten()[$key - 2];
            }
        } catch (\Exception $e) {
            Log::info("SERVER_ERROR: getFirstStrValue(): " . $e->getMessage());
        }
        return '';
    }

    public function getCarrier() {
        try {
            if (!empty($this->readEdiDataValue('NAD+CA'))) {
                $carrier = collect($this->readEdiDataValue('NAD+CA'))->flatten()[1];
                return strtoupper(strtok(trim($carrier), " "));
            }
        } catch (\Exception $e) {
            Log::info("SERVER_ERROR: getCarrier(): " . $e->getMessage());
        }
        return '';
    }

    public function getNameByEmail($value = '') {
        $v = explode('@', $value);
        return !empty($v[0]) ? ucwords(str_replace('.', ' ', $v[0])) : '';
    }

    public function getComValue() {
        $return_data = [];
        $return_data['carrier'] = ['email_name' => '', 'email' => ''];
        $return_data['owner'] = ['email_name' => '', 'email' => ''];
        $com_sigments = [];
        try {
            if (!empty($this->line_segments)) {
                foreach ($this->line_segments as $segments) {
                    $segments_array = explode("+", $segments);
                    $segments_first = isset($segments_array[0]) ? $segments_array[0] : '';
                    $segments_second = isset($segments_array[1]) ? $segments_array[1] : '';

                    if ($segments_first == "NAD" || $segments_first == "COM" || $segments_second == "IC") {
                        $com_array = explode(":", end($segments_array));

                        if ($segments_first == "NAD") {
                            $nad_name = $segments_second;
                            $customer_name = '';
                        }
                        if ($segments_second == "IC") {
                            $customer_name = end($com_array);
                        }


                        $com_key = !empty($com_array[1]) ? strtoupper($com_array[1]) : '';
                        $com_val = !empty($com_array[0]) ? $com_array[0] : '';

                        if ($com_key == "EM" && isset($customer_name) && $customer_name == '') {
                            $customer_name = $this->getNameByEmail($com_val);
                        }

                        if ($segments_first == "COM" && isset($nad_name)) {
                            if (!empty($com_key) && !empty($com_val)) {
                                $return_data[$nad_name]['customer_name'] = ucwords(strtolower($customer_name));
                                $return_data[$nad_name][$com_key][] = strtolower($com_val);
                            }
                        }
                    }
                }

                $return_data['carrier'] = [
                    'email_name' => isset($return_data['CA']['customer_name']) ? $return_data['CA']['customer_name'] : '',
                    'email' => isset($return_data['CA']['EM'][0]) ? $return_data['CA']['EM'][0] : '',
                ];
                $return_data['owner'] = [
                    'email_name' => isset($return_data['HI']['customer_name']) ? $return_data['HI']['customer_name'] : '',
                    'email' => isset($return_data['HI']['EM'][0]) ? $return_data['HI']['EM'][0] : '',
                ];
            }
        } catch (\Exception $e) {
            Log::info("SERVER_ERROR: getComValue(): " . $e->getMessage() . ' Line NO ' . $e->getLine());
        }
        return $return_data;
    }

    public function fileLineBreak($contents) {
        $contents = str_replace("?'", "question_with_single_code", $contents);
        $contents = explode("'", $contents);
        $contents = str_replace("question_with_single_code", "?'", $contents);
        return array_map('trim', $contents);
    }

    public function colonSeparator($line = '') {
        return str_replace(":", " ", trim($line));
    }

    public function getEdiDate($segment = 'UNB') {
        try {
            $datetime = $this->readEdiDataValue($segment);
            $datetime = trim($datetime[0][3][0] . $datetime[0][3][1]);
            $length = strlen("$datetime");

            if ($length == 10) {
                $date = preg_replace('/(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/', '20$1-$2-$3 $4:$5:00', $datetime);
            } else if ($length == 12) {
                $date = preg_replace('/(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})/', '$1-$2-$3 $4:$5:00', $datetime);
            }

            if ($this->validateDate($date)) {
                return $date;
            }
            return '';
        } catch (\Exception $e) {
            Log::info("SERVER_ERROR: getEdiDate(): " . $e->getMessage());
            return '';
        }
    }

    public function validateDate($date, $format = 'Y-m-d H:i:s') {
        try {
            $d = \DateTime::createFromFormat($format, "$date");
            return $d && $d->format($format) === "$date";
        } catch (\Exception $e) {
            Log::info("SERVER_ERROR: validateDate(): " . $e->getMessage());
            return '';
        }
    }

    public function getCountryCode($segment = "NAD+HI") {
        try {
            $line = $this->readEdiDataValue($segment);
            $last_sigment = isset($line[0]) ? end($line[0]) : [];
            return end($last_sigment);
        } catch (\Exception $e) {
            Log::info("SERVER_ERROR: getCountryCode(): " . $e->getMessage() . ' Line NO:' . $e->getLine());
            return '';
        }
    }

}
