<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EdiController;
use App\Http\Controllers\EdiAutomationController;

/*
  |--------------------------------------------------------------------------
  | API Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register API routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | is assigned the "api" middleware group. Enjoy building your API!
  |
 */

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->post('/edimessage/{type?}', [EdiController::class, 'storeMessage']);
Route::middleware('auth:api')->post('/edimessagefile', [EdiController::class, 'storeMessageFile']);
Route::middleware('auth:api')->post('/edi-file-base64', [EdiController::class, 'storeEdiFilebase64']);
Route::get('/edi-file-automation', [EdiAutomationController::class, 'ediAutomation']); //downlod from FTP and process
Route::get('/edi-file-download', [EdiAutomationController::class, 'ediFileDownload']);

Route::get('/edi/process-and-compare', [EdiAutomationController::class, 'ediProcessANDCompareAutomation']);
Route::get('/edi-file-compare', [EdiAutomationController::class, 'ediProcessANDCompareAutomation']); //this will delete
Route::get('/edi/outgoing-process', [EdiAutomationController::class, 'ediOutgoingProcess']);
Route::get('/edi/incoming-process', [EdiAutomationController::class, 'ediIncomingProcess']);
Route::get('/edi/compare', [EdiAutomationController::class, 'ediCompare']);
