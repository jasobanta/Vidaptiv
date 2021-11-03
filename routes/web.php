<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EdiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RulesController;
use App\Http\Controllers\CarrierController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\EmailHistoryController;
use App\Http\Controllers\EdiTitleController;
use App\Http\Controllers\RulesTitleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EdiEmailController;
use App\Http\Controllers\EdiSendCompareEmail;
use App\Http\Controllers\EmailAttachmentController;
use App\Http\Controllers\CommonMigrationController;
use App\Http\Controllers\FileLogController;

/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
 */

Route::get('/', [DashboardController::class, 'show'])->middleware(['auth'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'show'])->middleware(['auth'])->name('dashboard');
Route::post('/dashboard/ajax', [DashboardController::class, 'ajax'])->middleware(['auth'])->name('dashboard.datatable.ajax');

Route::get('/filelog/carrier/{scac}', [FilelogController::class, 'showCarrier'])->middleware(['auth'])->name('filelog.carrier.list');
Route::post('/filelog/ajax', [FilelogController::class, 'ajax'])->middleware(['auth'])->name('filelog.datatable.ajax');


Route::get('/process-edi/{type}', [EdiController::class, 'processEdi'])
        ->middleware('auth')->name('process.Edi');
Route::get('/compare-edi', [EdiController::class, 'CompareEdi'])
        ->middleware('auth')->name('Compare.Edi');
Route::post('/show-file', [EdiController::class, 'showfile'])
        ->middleware('auth')->name('show.file');

Route::post('/show-file-action', [EdiController::class, 'showfileAction'])
        ->middleware('auth')->name('show.file.action');
Route::get('/show-file-action', [EdiController::class, 'showfileAction'])
        ->middleware('auth')->name('show.file.action');

Route::post('/diff-download', [EdiController::class, 'diffDownload'])
        ->middleware('auth')->name('diff.download');

Route::post('/save-email-attachment', [EdiController::class, 'saveEmailAttachment'])
        ->middleware('auth')->name('save.email.attachment');

Route::get('/process-outgoing-edi', [EdiController::class, 'processOutgoingEdi'])
        ->middleware('auth')->name('process.outgoing.edi');

Route::get('/process-incoming-edi/{scac}/{carrier_id?}', [EdiController::class, 'processIncomingEdi'])
        ->middleware('auth')->name('process.outgoing.edi');


Route::group(['middleware' => 'auth'], function () {
    Route::get('/file/logs', [FileLogController::class, 'index'])->name('filelog.list');
    Route::get('/email/history', [EmailHistoryController::class, 'index'])->name('emailhistory.list');
    Route::get('/email/history/formstate/{id}', [EmailHistoryController::class, 'formstate'])->name('emailhistory.formstate');
    Route::get('/email/history/{id}/{hash}', [EmailHistoryController::class, 'showById'])->name('show-email-history.list');

    Route::get('/download-email-attachment', [EmailAttachmentController::class, 'download'])->name('emailattachment.download');

    Route::get('/email/list', [EmailController::class, 'index'])->name('email.list');
    Route::get('/email/add', [EmailController::class, 'add'])->name('email.add');
    Route::post('/email/add', [EmailController::class, 'save'])->name('email.add');
    Route::get('/email/edit/{id}', [EmailController::class, 'edit'])->name('email.edit');
    Route::post('/email/update/{id}', [EmailController::class, 'save'])->name('email.update');
    Route::get('/email/delete/{id}', [EmailController::class, 'delete'])->name('email.delete');

    Route::get('/carrier/list', [CarrierController::class, 'index'])->name('carrier.list');
    Route::get('/carrier/add', [CarrierController::class, 'add'])->name('carrier.add');
    Route::post('/carrier/add', [CarrierController::class, 'save'])->name('carrier.add');
    Route::get('/carrier/edit/{id}', [CarrierController::class, 'edit'])->name('carrier.edit');
    Route::post('/carrier/update/{id}', [CarrierController::class, 'save'])->name('carrier.update');
    Route::get('/carrier/delete/{id}', [CarrierController::class, 'delete'])->name('carrier.delete');

    Route::get('/edi-title/list', [EdiTitleController::class, 'index'])->name('edi-title.list');
    Route::get('/edi-title/add', [EdiTitleController::class, 'add'])->name('edi-title.add');
    Route::post('/edi-title/add', [EdiTitleController::class, 'save'])->name('edi-title.add');
    Route::get('/edi-title/edit/{id}', [EdiTitleController::class, 'edit'])->name('edi-title.edit');
    Route::post('/edi-title/update/{id}', [EdiTitleController::class, 'save'])->name('edi-title.update');
    Route::get('/edi-title/delete/{id}', [EdiTitleController::class, 'delete'])->name('edi-title.delete');

    Route::get('/rules-title/list', [RulesTitleController::class, 'index'])->name('rules-title.list');
    Route::get('/rules-title/add', [RulesTitleController::class, 'add'])->name('rules-title.add');
    Route::post('/rules-title/add', [RulesTitleController::class, 'save'])->name('rules-title.add');
    Route::get('/rules-title/edit/{id}', [RulesTitleController::class, 'edit'])->name('rules-title.edit');
    Route::post('/rules-title/update/{id}', [RulesTitleController::class, 'save'])->name('rules-title.update');
    Route::get('/rules-title/delete/{id}', [RulesTitleController::class, 'delete'])->name('rules-title.delete');

    Route::get('/rule/list', [RulesController::class, 'index'])->name('rule.list');
    Route::get('/rule/edit/{id}', [RulesController::class, 'edit'])->name('rule.edit');
    Route::post('/rule/update/{id}', [RulesController::class, 'save'])->name('rule.update');
    Route::get('/rule/update/priority/{id}/{priority}', [RulesController::class, 'updatePriority'])->name('rule.update.priority');

    Route::get('/rule/carrier/list', [RulesController::class, 'ruleCarrierList'])->name('rule.carrier.list');
    Route::post('/rule/carrier/update/{id}', [RulesController::class, 'ruleCarrierUpdate'])->name('rule.carrier.update');
    Route::get('/rule/carrier/edit/{id}', [RulesController::class, 'ruleCarrierEdit'])->name('rule.carrier.edit');
    Route::post('/rule/carrier/add-ignore/{id}', [RulesController::class, 'ruleCarrieAddIgnore'])->name('rule.carrier.add.ignore');
    Route::post('/rule/carrier/delete-ignore/{id}', [RulesController::class, 'ruleCarrieDeleteIgnore'])->name('rule.carrier.delete.ignore');

    Route::get('/rule/segment/list', [RulesController::class, 'ruleSegmentList'])->name('rule.segment.list');
    Route::post('/rule/segment/add', [RulesController::class, 'ruleSegmentAdd'])->name('rule.segment.add');
    Route::get('/rule/segment/delete/{id}', [RulesController::class, 'ruleSegmentDelete'])->name('rule.segment.delete');

    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::get('/user/add', [UserController::class, 'add'])->name('user.add');
    Route::post('/user/add', [UserController::class, 'save'])->name('user.add');
    Route::get('/user/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
    Route::post('/user/update/{id}', [UserController::class, 'save'])->name('user.update');
    Route::get('/user/delete/{id}', [UserController::class, 'delete'])->name('user.delete');

    Route::get('/edi/download', [EdiController::class, 'download'])->name('edi.download');
    Route::post('/edi/action-form', [EdiEmailController::class, 'actionForm'])->name('edi.action-form');
    Route::get('/edi/email-form/{id}', [EdiEmailController::class, 'emailForm'])->name('edi.email-form');
    Route::post('/edi/send-email', [EdiEmailController::class, 'sendEmail'])->name('edi.send-email');
    Route::post('/save-edi-action', [EdiEmailController::class, 'saveediAction'])->name('actionform.save');
    Route::post('/save-accept-action', [EdiEmailController::class, 'saveAcceptAction'])->name('accept.save');
    Route::get('/edi/email-template/{edi_id}/{type_id}', [EdiEmailController::class, 'ediEmailTemplate'])->name('edi.email.template');
    Route::get('/view-edi-diff/{key?}', [EdiSendCompareEmail::class, 'viewEdiDiff'])->name('edi.view-edi-diff');
    Route::post('/edi/download-form', [EdiEmailController::class, 'downloadForm'])->name('edi.download-form');
    Route::post('/save-document-status', [EdiController::class, 'saveDocumentStatus'])->name('edi.document-status');

    Route::get('/update-edi-data', [CommonMigrationController::class, 'updateEdiData']);
});

require __DIR__ . '/auth.php';
