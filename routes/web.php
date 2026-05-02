<?php

use App\Http\Controllers\AccountsController;
use App\Http\Controllers\AdminLeaveController;

// use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\UserController;
use App\Http\Controllers\AllInternsController;
use App\Http\Controllers\apps\AcademyCourse;
use App\Http\Controllers\apps\AcademyCourseDetails;
use App\Http\Controllers\apps\AcademyDashboard;
use App\Http\Controllers\apps\AccessPermission;
use App\Http\Controllers\apps\AccessRoles;
use App\Http\Controllers\apps\Calendar;
use App\Http\Controllers\apps\Chat;
use App\Http\Controllers\apps\EcommerceCustomerAll;
use App\Http\Controllers\apps\EcommerceCustomerDetailsBilling;
use App\Http\Controllers\apps\EcommerceCustomerDetailsNotifications;
use App\Http\Controllers\apps\EcommerceCustomerDetailsOverview;
use App\Http\Controllers\apps\EcommerceCustomerDetailsSecurity;
use App\Http\Controllers\apps\EcommerceDashboard;
use App\Http\Controllers\apps\EcommerceManageReviews;
use App\Http\Controllers\apps\EcommerceOrderDetails;
use App\Http\Controllers\apps\EcommerceOrderList;
use App\Http\Controllers\apps\EcommerceProductAdd;
use App\Http\Controllers\apps\EcommerceProductCategory;
use App\Http\Controllers\apps\EcommerceProductList;
use App\Http\Controllers\apps\EcommerceReferrals;
use App\Http\Controllers\apps\EcommerceSettingsCheckout;
use App\Http\Controllers\apps\EcommerceSettingsDetails;
use App\Http\Controllers\apps\EcommerceSettingsLocations;
use App\Http\Controllers\apps\EcommerceSettingsNotifications;
use App\Http\Controllers\apps\EcommerceSettingsPayments;
use App\Http\Controllers\apps\EcommerceSettingsShipping;
use App\Http\Controllers\apps\Email;
use App\Http\Controllers\apps\InvoiceAdd;
use App\Http\Controllers\apps\InvoiceEdit;
use App\Http\Controllers\apps\InvoiceList;
use App\Http\Controllers\apps\InvoicePreview;
use App\Http\Controllers\apps\InvoicePrint;
use App\Http\Controllers\apps\Kanban;
use App\Http\Controllers\apps\LogisticsDashboard;
use App\Http\Controllers\apps\LogisticsFleet;
use App\Http\Controllers\apps\UserList;
use App\Http\Controllers\apps\UserViewAccount;
use App\Http\Controllers\apps\UserViewBilling;
use App\Http\Controllers\apps\UserViewConnections;
use App\Http\Controllers\apps\UserViewNotifications;
use App\Http\Controllers\apps\UserViewSecurity;
use App\Http\Controllers\authentications\ForgotPasswordBasic;
use App\Http\Controllers\authentications\ForgotPasswordCover;
use App\Http\Controllers\authentications\LoginBasic;
use App\Http\Controllers\authentications\LoginCover;
use App\Http\Controllers\authentications\RegisterBasic;
use App\Http\Controllers\authentications\RegisterCover;
use App\Http\Controllers\authentications\RegisterMultiSteps;
use App\Http\Controllers\authentications\ResetPasswordBasic;
use App\Http\Controllers\authentications\ResetPasswordCover;
use App\Http\Controllers\authentications\TwoStepsBasic;
use App\Http\Controllers\authentications\TwoStepsCover;
use App\Http\Controllers\authentications\VerifyEmailBasic;
use App\Http\Controllers\authentications\VerifyEmailCover;
use App\Http\Controllers\cards\CardActions;
use App\Http\Controllers\cards\CardAdvance;
use App\Http\Controllers\cards\CardAnalytics;
use App\Http\Controllers\cards\CardBasic;
use App\Http\Controllers\cards\CardGamifications;
use App\Http\Controllers\cards\CardStatistics;
use App\Http\Controllers\charts\ApexCharts;
use App\Http\Controllers\charts\ChartJs;
use App\Http\Controllers\dashboard\Analytics;
use App\Http\Controllers\dashboard\Crm;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\extended_ui\Avatar;
use App\Http\Controllers\extended_ui\BlockUI;
use App\Http\Controllers\extended_ui\DragAndDrop;
use App\Http\Controllers\extended_ui\MediaPlayer;
use App\Http\Controllers\extended_ui\Misc;
use App\Http\Controllers\extended_ui\PerfectScrollbar;
use App\Http\Controllers\extended_ui\StarRatings;
use App\Http\Controllers\extended_ui\SweetAlert;
use App\Http\Controllers\extended_ui\TextDivider;
use App\Http\Controllers\extended_ui\TimelineBasic;
use App\Http\Controllers\extended_ui\TimelineFullscreen;
use App\Http\Controllers\extended_ui\Tour;
use App\Http\Controllers\extended_ui\Treeview;
use App\Http\Controllers\EscalationController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\form_elements\BasicInput;
use App\Http\Controllers\form_elements\CustomOptions;
use App\Http\Controllers\form_elements\Editors;
use App\Http\Controllers\form_elements\Extras;
use App\Http\Controllers\form_elements\FileUpload;
use App\Http\Controllers\form_elements\InputGroups;
use App\Http\Controllers\form_elements\Picker;
use App\Http\Controllers\form_elements\Selects;
use App\Http\Controllers\form_elements\Sliders;
use App\Http\Controllers\form_elements\Switches;
use App\Http\Controllers\form_layouts\HorizontalForm;
use App\Http\Controllers\form_layouts\StickyActions;
use App\Http\Controllers\form_layouts\VerticalForm;
use App\Http\Controllers\form_validation\Validation;
use App\Http\Controllers\form_wizard\Icons as FormWizardIcons;
use App\Http\Controllers\form_wizard\Numbered as FormWizardNumbered;
use App\Http\Controllers\front_pages\Checkout;
use App\Http\Controllers\front_pages\HelpCenter;
use App\Http\Controllers\front_pages\HelpCenterArticle;
use App\Http\Controllers\front_pages\Landing;
use App\Http\Controllers\front_pages\Payment;
use App\Http\Controllers\front_pages\Pricing;
use App\Http\Controllers\icons\FontAwesome;
use App\Http\Controllers\icons\Tabler;
use App\Http\Controllers\InternAccountsController;
use App\Http\Controllers\InternProjectsController;
use App\Http\Controllers\InternTaskController;
use App\Http\Controllers\intern\InternDashboardController;
use App\Http\Controllers\intern\InternProfileController;
use App\Http\Controllers\intern\InternSettingsController;
use App\Http\Controllers\intern\InternResourceController;
use App\Http\Controllers\intern\InternProjectController;
use App\Http\Controllers\intern\InternLeaveController;
use App\Http\Controllers\intern\InternInvoiceController;
use App\Http\Controllers\intern\InternFeedbackController;
use App\Http\Controllers\intern\InternTaskController as InternPortalTaskController;
use App\Http\Controllers\intern\InternCertificateController;
use App\Http\Controllers\intern\InternAttendanceController;
use App\Http\Controllers\intern\InternOfferLetterController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\KnowledgeBaseController;
use App\Http\Controllers\language\LanguageController;
use App\Http\Controllers\laravel_example\UserManagement;
use App\Http\Controllers\layouts\Blank;
use App\Http\Controllers\layouts\CollapsedMenu;
use App\Http\Controllers\layouts\Container;
use App\Http\Controllers\layouts\ContentNavbar;
use App\Http\Controllers\layouts\ContentNavSidebar;
use App\Http\Controllers\layouts\Fluid;
use App\Http\Controllers\layouts\Horizontal;
use App\Http\Controllers\layouts\Vertical;
use App\Http\Controllers\layouts\WithoutMenu;
use App\Http\Controllers\layouts\WithoutNavbar;
use App\Http\Controllers\leavecontroller;
use App\Http\Controllers\manager_controllers\AllManagerInternController;
use App\Http\Controllers\manager_controllers\CertificateController;
use App\Http\Controllers\manager_controllers\CommunicationController;
use App\Http\Controllers\manager_controllers\DashboardManagerController;
use App\Http\Controllers\manager_controllers\InternationalInternsManagerController;
use App\Http\Controllers\manager_controllers\InternPerformanceController;
use App\Http\Controllers\manager_controllers\InvoiceController as ManagerInvoiceController;
use App\Http\Controllers\manager_controllers\ManagerAttendanceController;
use App\Http\Controllers\manager_controllers\ManagerCurriculumController;
use App\Http\Controllers\manager_controllers\ManagerCurriculumProjectController;
use App\Http\Controllers\manager_controllers\ManagerKnowledgeBaseController;
use App\Http\Controllers\manager_controllers\ManagerLeaveController;
use App\Http\Controllers\manager_controllers\OfferLetterRequestController;
use App\Http\Controllers\manager_controllers\OfferLetterTemplateController;
use App\Http\Controllers\manager_controllers\PaymentReceiptController;
use App\Http\Controllers\manager_controllers\ProfileSettingsController;
use App\Http\Controllers\manager_controllers\RemainingAmountController;
use App\Http\Controllers\manager_controllers\RevenueController;
use App\Http\Controllers\manager_controllers\Supervisorcontroller;
use App\Http\Controllers\manager_controllers\TaskViewController;
use App\Http\Controllers\ManagersController;
use App\Http\Controllers\maps\Leaflet;
use App\Http\Controllers\modal\ModalExample;
use App\Http\Controllers\OTPVerifyController;
use App\Http\Controllers\pages\AccountSettingsAccount;
use App\Http\Controllers\pages\AccountSettingsBilling;
use App\Http\Controllers\pages\AccountSettingsConnections;
use App\Http\Controllers\pages\AccountSettingsNotifications;
use App\Http\Controllers\pages\AccountSettingsSecurity;
use App\Http\Controllers\pages\Faq;
use App\Http\Controllers\pages\MiscComingSoon;
use App\Http\Controllers\pages\MiscError;
use App\Http\Controllers\pages\MiscNotAuthorized;
use App\Http\Controllers\pages\MiscUnderMaintenance;
use App\Http\Controllers\pages\Pricing as PagesPricing;
use App\Http\Controllers\pages\UserConnections;
use App\Http\Controllers\pages\UserProfile;
use App\Http\Controllers\pages\UserProjects;
use App\Http\Controllers\pages\UserTeams;
use App\Http\Controllers\ProjectTaskController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\supervisor_controllers\DashboardSupervisorController;
use App\Http\Controllers\supervisor_controllers\SupervisorAttendanceController;
use App\Http\Controllers\supervisor_controllers\SupervisorEvaluationController;
use App\Http\Controllers\supervisor_controllers\SupervisorFeedbackController;
use App\Http\Controllers\supervisor_controllers\SupervisorInternController;
use App\Http\Controllers\supervisor_controllers\SupervisorKnowledgeBaseController;
use App\Http\Controllers\supervisor_controllers\SupervisorLeaveController;
use App\Http\Controllers\supervisor_controllers\SupervisorProfileController;
use App\Http\Controllers\supervisor_controllers\SupervisorProjectController;
use App\Http\Controllers\supervisor_controllers\SupervisorTaskController;
use App\Http\Controllers\SupervisorsController;
use App\Http\Controllers\tables\Basic as TablesBasic;
use App\Http\Controllers\tables\DatatableAdvanced;
use App\Http\Controllers\tables\DatatableBasic;
use App\Http\Controllers\tables\DatatableExtensions;
use App\Http\Controllers\TechnologyController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\user_interface\Accordion;
use App\Http\Controllers\user_interface\Alerts;
use App\Http\Controllers\user_interface\Badges;
use App\Http\Controllers\user_interface\Buttons;
use App\Http\Controllers\user_interface\Carousel;
use App\Http\Controllers\user_interface\Collapse;
use App\Http\Controllers\user_interface\Dropdowns;
use App\Http\Controllers\user_interface\Footer;
use App\Http\Controllers\user_interface\ListGroups;
use App\Http\Controllers\user_interface\Modals;
use App\Http\Controllers\user_interface\Navbar;
use App\Http\Controllers\user_interface\Offcanvas;
use App\Http\Controllers\user_interface\PaginationBreadcrumbs;
use App\Http\Controllers\user_interface\Progress;
use App\Http\Controllers\user_interface\Spinners;
use App\Http\Controllers\user_interface\TabsPills;
use App\Http\Controllers\user_interface\Toasts;
use App\Http\Controllers\user_interface\TooltipsPopovers;
use App\Http\Controllers\user_interface\Typography;
use App\Http\Controllers\WithdrawManagerController;
use App\Http\Controllers\WithdrawAdminController;
use App\Http\Controllers\wizard_example\Checkout as WizardCheckout;
use App\Http\Controllers\wizard_example\CreateDeal;
use App\Http\Controllers\wizard_example\PropertyListing;
use App\Http\Middleware\validManager;
use App\Http\Middleware\ValidUser;
use App\Http\Controllers\InternPublicRegistrationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ChatController;


// Main Page Route
// Route::get('/', [Analytics::class, 'index'])->name('dashboard-analytics');
// Route::get('/dashboard/analytics', [Analytics::class, 'index'])->name('dashboard-analytics');
Route::get('/dashboard/crm', [Crm::class, 'index'])->name('dashboard-crm');
// locale
Route::get('/lang/{locale}', [LanguageController::class, 'swap']);

// layout
Route::get('/layouts/collapsed-menu', [CollapsedMenu::class, 'index'])->name('layouts-collapsed-menu');
Route::get('/layouts/content-navbar', [ContentNavbar::class, 'index'])->name('layouts-content-navbar');
Route::get('/layouts/content-nav-sidebar', [ContentNavSidebar::class, 'index'])->name('layouts-content-nav-sidebar');
// Route::get('/layouts/navbar-full', [NavbarFull::class, 'index'])->name('layouts-navbar-full');
// Route::get('/layouts/navbar-full-sidebar', [NavbarFullSidebar::class, 'index'])->name('layouts-navbar-full-sidebar');
Route::get('/layouts/horizontal', [Horizontal::class, 'index'])->name('layouts-horizontal');
Route::get('/layouts/vertical', [Vertical::class, 'index'])->name('layouts-vertical');
Route::get('/layouts/without-menu', [WithoutMenu::class, 'index'])->name('layouts-without-menu');
Route::get('/layouts/without-navbar', [WithoutNavbar::class, 'index'])->name('layouts-without-navbar');
Route::get('/layouts/fluid', [Fluid::class, 'index'])->name('layouts-fluid');
Route::get('/layouts/container', [Container::class, 'index'])->name('layouts-container');
Route::get('/layouts/blank', [Blank::class, 'index'])->name('layouts-blank');

// Front Pages
Route::get('/front-pages/landing', [Landing::class, 'index'])->name('front-pages-landing');
Route::get('/front-pages/pricing', [Pricing::class, 'index'])->name('front-pages-pricing');
Route::get('/front-pages/payment', [Payment::class, 'index'])->name('front-pages-payment');
Route::get('/front-pages/checkout', [Checkout::class, 'index'])->name('front-pages-checkout');
Route::get('/front-pages/help-center', [HelpCenter::class, 'index'])->name('front-pages-help-center');
Route::get('/front-pages/help-center-article', [HelpCenterArticle::class, 'index'])->name('front-pages-help-center-article');

// apps
Route::get('/app/email', [Email::class, 'index'])->name('app-email');
Route::get('/app/chat', [Chat::class, 'index'])->name('app-chat');
Route::get('/app/calendar', [Calendar::class, 'index'])->name('app-calendar');
Route::get('/app/kanban', [Kanban::class, 'index'])->name('app-kanban');
Route::get('/app/ecommerce/dashboard', [EcommerceDashboard::class, 'index'])->name('app-ecommerce-dashboard');
Route::get('/app/ecommerce/product/list', [EcommerceProductList::class, 'index'])->name('app-ecommerce-product-list');
Route::get('/app/ecommerce/product/add', [EcommerceProductAdd::class, 'index'])->name('app-ecommerce-product-add');
Route::get('/app/ecommerce/product/category', [EcommerceProductCategory::class, 'index'])->name('app-ecommerce-product-category');
Route::get('/app/ecommerce/order/list', [EcommerceOrderList::class, 'index'])->name('app-ecommerce-order-list');
Route::get('/app/ecommerce/order/details', [EcommerceOrderDetails::class, 'index'])->name('app-ecommerce-order-details');
Route::get('/app/ecommerce/customer/all', [EcommerceCustomerAll::class, 'index'])->name('app-ecommerce-customer-all');
Route::get('/app/ecommerce/customer/details/overview', [EcommerceCustomerDetailsOverview::class, 'index'])->name('app-ecommerce-customer-details-overview');
Route::get('/app/ecommerce/customer/details/security', [EcommerceCustomerDetailsSecurity::class, 'index'])->name('app-ecommerce-customer-details-security');
Route::get('/app/ecommerce/customer/details/billing', [EcommerceCustomerDetailsBilling::class, 'index'])->name('app-ecommerce-customer-details-billing');
Route::get('/app/ecommerce/customer/details/notifications', [EcommerceCustomerDetailsNotifications::class, 'index'])->name('app-ecommerce-customer-details-notifications');
Route::get('/app/ecommerce/manage/reviews', [EcommerceManageReviews::class, 'index'])->name('app-ecommerce-manage-reviews');
Route::get('/app/ecommerce/referrals', [EcommerceReferrals::class, 'index'])->name('app-ecommerce-referrals');
Route::get('/app/ecommerce/settings/details', [EcommerceSettingsDetails::class, 'index'])->name('app-ecommerce-settings-details');
Route::get('/app/ecommerce/settings/payments', [EcommerceSettingsPayments::class, 'index'])->name('app-ecommerce-settings-payments');
Route::get('/app/ecommerce/settings/checkout', [EcommerceSettingsCheckout::class, 'index'])->name('app-ecommerce-settings-checkout');
Route::get('/app/ecommerce/settings/shipping', [EcommerceSettingsShipping::class, 'index'])->name('app-ecommerce-settings-shipping');
Route::get('/app/ecommerce/settings/locations', [EcommerceSettingsLocations::class, 'index'])->name('app-ecommerce-settings-locations');
Route::get('/app/ecommerce/settings/notifications', [EcommerceSettingsNotifications::class, 'index'])->name('app-ecommerce-settings-notifications');
Route::get('/app/academy/dashboard', [AcademyDashboard::class, 'index'])->name('app-academy-dashboard');
Route::get('/app/academy/course', [AcademyCourse::class, 'index'])->name('app-academy-course');
Route::get('/app/academy/course-details', [AcademyCourseDetails::class, 'index'])->name('app-academy-course-details');
Route::get('/app/logistics/dashboard', [LogisticsDashboard::class, 'index'])->name('app-logistics-dashboard');
Route::get('/app/logistics/fleet', [LogisticsFleet::class, 'index'])->name('app-logistics-fleet');
Route::get('/app/invoice/list', [InvoiceList::class, 'index'])->name('app-invoice-list');
Route::get('/app/invoice/preview', [InvoicePreview::class, 'index'])->name('app-invoice-preview');
Route::get('/app/invoice/print', [InvoicePrint::class, 'index'])->name('app-invoice-print');
Route::get('/app/invoice/edit', [InvoiceEdit::class, 'index'])->name('app-invoice-edit');
Route::get('/app/invoice/add', [InvoiceAdd::class, 'index'])->name('app-invoice-add');











Route::get('/app/user/view/account', [UserViewAccount::class, 'index'])->name('app-user-view-account');
Route::get('/app/user/view/security', [UserViewSecurity::class, 'index'])->name('app-user-view-security');
Route::get('/app/user/view/billing', [UserViewBilling::class, 'index'])->name('app-user-view-billing');
Route::get('/app/user/view/notifications', [UserViewNotifications::class, 'index'])->name('app-user-view-notifications');
Route::get('/app/user/view/connections', [UserViewConnections::class, 'index'])->name('app-user-view-connections');
Route::get('/app/access-roles', [AccessRoles::class, 'index'])->name('app-access-roles');
Route::get('/app/access-permission', [AccessPermission::class, 'index'])->name('app-access-permission');

// pages
Route::get('/pages/profile-user', [UserProfile::class, 'index'])->name('pages-profile-user');
Route::get('/pages/profile-teams', [UserTeams::class, 'index'])->name('pages-profile-teams');
Route::get('/pages/profile-projects', [UserProjects::class, 'index'])->name('pages-profile-projects');
Route::get('/pages/profile-connections', [UserConnections::class, 'index'])->name('pages-profile-connections');
Route::get('/pages/account-settings-account', [AccountSettingsAccount::class, 'index'])->name('pages-account-settings-account');
Route::get('/pages/account-settings-security', [AccountSettingsSecurity::class, 'index'])->name('pages-account-settings-security');
Route::get('/pages/account-settings-billing', [AccountSettingsBilling::class, 'index'])->name('pages-account-settings-billing');
Route::get('/pages/account-settings-notifications', [AccountSettingsNotifications::class, 'index'])->name('pages-account-settings-notifications');
Route::get('/pages/account-settings-connections', [AccountSettingsConnections::class, 'index'])->name('pages-account-settings-connections');
Route::get('/pages/faq', [Faq::class, 'index'])->name('pages-faq');
Route::get('/pages/pricing', [PagesPricing::class, 'index'])->name('pages-pricing');
Route::get('/pages/misc-error', [MiscError::class, 'index'])->name('pages-misc-error');
Route::get('/pages/misc-under-maintenance', [MiscUnderMaintenance::class, 'index'])->name('pages-misc-under-maintenance');
Route::get('/pages/misc-comingsoon', [MiscComingSoon::class, 'index'])->name('pages-misc-comingsoon');
Route::get('/pages/misc-not-authorized', [MiscNotAuthorized::class, 'index'])->name('pages-misc-not-authorized');

// authentication
Route::get('/auth/login-basic', [LoginBasic::class, 'index'])->name('auth-login-basic');
// Route::get('/auth/login-cover', [LoginCover::class, 'index'])->name('auth-login-cover');
Route::get('/auth/register-basic', [RegisterBasic::class, 'index'])->name('auth-register-basic');
Route::get('/auth/register-cover', [RegisterCover::class, 'index'])->name('auth-register-cover');
Route::get('/auth/register-multisteps', [RegisterMultiSteps::class, 'index'])->name('auth-register-multisteps');
Route::get('/auth/verify-email-basic', [VerifyEmailBasic::class, 'index'])->name('auth-verify-email-basic');
Route::get('/auth/verify-email-cover', [VerifyEmailCover::class, 'index'])->name('auth-verify-email-cover');
Route::get('/auth/reset-password-basic', [ResetPasswordBasic::class, 'index'])->name('auth-reset-password-basic');
Route::get('/auth/reset-password-cover', [ResetPasswordCover::class, 'index'])->name('auth-reset-password-cover');
Route::get('/auth/forgot-password-basic', [ForgotPasswordBasic::class, 'index'])->name('auth-forgot-password-basic');
Route::get('/auth/forgot-password', [ForgotPasswordCover::class, 'index'])->name('auth-forgot-password-cover');
Route::get('/auth/two-steps-basic', [TwoStepsBasic::class, 'index'])->name('auth-two-steps-basic');
Route::get('/auth/two-steps-cover', [TwoStepsCover::class, 'index'])->name('auth-two-steps-cover');

// wizard example
Route::get('/wizard/ex-checkout', [WizardCheckout::class, 'index'])->name('wizard-ex-checkout');
Route::get('/wizard/ex-property-listing', [PropertyListing::class, 'index'])->name('wizard-ex-property-listing');
Route::get('/wizard/ex-create-deal', [CreateDeal::class, 'index'])->name('wizard-ex-create-deal');

// modal
Route::get('/modal-examples', [ModalExample::class, 'index'])->name('modal-examples');

// cards
Route::get('/cards/basic', [CardBasic::class, 'index'])->name('cards-basic');
Route::get('/cards/advance', [CardAdvance::class, 'index'])->name('cards-advance');
Route::get('/cards/statistics', [CardStatistics::class, 'index'])->name('cards-statistics');
Route::get('/cards/analytics', [CardAnalytics::class, 'index'])->name('cards-analytics');
Route::get('/cards/gamifications', [CardGamifications::class, 'index'])->name('cards-gamifications');
Route::get('/cards/actions', [CardActions::class, 'index'])->name('cards-actions');

// User Interface
Route::get('/ui/accordion', [Accordion::class, 'index'])->name('ui-accordion');
Route::get('/ui/alerts', [Alerts::class, 'index'])->name('ui-alerts');
Route::get('/ui/badges', [Badges::class, 'index'])->name('ui-badges');
Route::get('/ui/buttons', [Buttons::class, 'index'])->name('ui-buttons');
Route::get('/ui/carousel', [Carousel::class, 'index'])->name('ui-carousel');
Route::get('/ui/collapse', [Collapse::class, 'index'])->name('ui-collapse');
Route::get('/ui/dropdowns', [Dropdowns::class, 'index'])->name('ui-dropdowns');
Route::get('/ui/footer', [Footer::class, 'index'])->name('ui-footer');
Route::get('/ui/list-groups', [ListGroups::class, 'index'])->name('ui-list-groups');
Route::get('/ui/modals', [Modals::class, 'index'])->name('ui-modals');
Route::get('/ui/navbar', [Navbar::class, 'index'])->name('ui-navbar');
Route::get('/ui/offcanvas', [Offcanvas::class, 'index'])->name('ui-offcanvas');
Route::get('/ui/pagination-breadcrumbs', [PaginationBreadcrumbs::class, 'index'])->name('ui-pagination-breadcrumbs');
Route::get('/ui/progress', [Progress::class, 'index'])->name('ui-progress');
Route::get('/ui/spinners', [Spinners::class, 'index'])->name('ui-spinners');
Route::get('/ui/tabs-pills', [TabsPills::class, 'index'])->name('ui-tabs-pills');
Route::get('/ui/toasts', [Toasts::class, 'index'])->name('ui-toasts');
Route::get('/ui/tooltips-popovers', [TooltipsPopovers::class, 'index'])->name('ui-tooltips-popovers');
Route::get('/ui/typography', [Typography::class, 'index'])->name('ui-typography');

// extended ui
Route::get('/extended/ui-avatar', [Avatar::class, 'index'])->name('extended-ui-avatar');
Route::get('/extended/ui-blockui', [BlockUI::class, 'index'])->name('extended-ui-blockui');
Route::get('/extended/ui-drag-and-drop', [DragAndDrop::class, 'index'])->name('extended-ui-drag-and-drop');
Route::get('/extended/ui-media-player', [MediaPlayer::class, 'index'])->name('extended-ui-media-player');
Route::get('/extended/ui-perfect-scrollbar', [PerfectScrollbar::class, 'index'])->name('extended-ui-perfect-scrollbar');
Route::get('/extended/ui-star-ratings', [StarRatings::class, 'index'])->name('extended-ui-star-ratings');
Route::get('/extended/ui-sweetalert2', [SweetAlert::class, 'index'])->name('extended-ui-sweetalert2');
Route::get('/extended/ui-text-divider', [TextDivider::class, 'index'])->name('extended-ui-text-divider');
Route::get('/extended/ui-timeline-basic', [TimelineBasic::class, 'index'])->name('extended-ui-timeline-basic');
Route::get('/extended/ui-timeline-fullscreen', [TimelineFullscreen::class, 'index'])->name('extended-ui-timeline-fullscreen');
Route::get('/extended/ui-tour', [Tour::class, 'index'])->name('extended-ui-tour');
Route::get('/extended/ui-treeview', [Treeview::class, 'index'])->name('extended-ui-treeview');
Route::get('/extended/ui-misc', [Misc::class, 'index'])->name('extended-ui-misc');

// icons
Route::get('/icons/tabler', [Tabler::class, 'index'])->name('icons-tabler');
Route::get('/icons/font-awesome', [FontAwesome::class, 'index'])->name('icons-font-awesome');

// form elements
Route::get('/forms/basic-inputs', [BasicInput::class, 'index'])->name('forms-basic-inputs');
Route::get('/forms/input-groups', [InputGroups::class, 'index'])->name('forms-input-groups');
Route::get('/forms/custom-options', [CustomOptions::class, 'index'])->name('forms-custom-options');
Route::get('/forms/editors', [Editors::class, 'index'])->name('forms-editors');
Route::get('/forms/file-upload', [FileUpload::class, 'index'])->name('forms-file-upload');
Route::get('/forms/pickers', [Picker::class, 'index'])->name('forms-pickers');
Route::get('/forms/selects', [Selects::class, 'index'])->name('forms-selects');
Route::get('/forms/sliders', [Sliders::class, 'index'])->name('forms-sliders');
Route::get('/forms/switches', [Switches::class, 'index'])->name('forms-switches');
Route::get('/forms/extras', [Extras::class, 'index'])->name('forms-extras');

// form layouts
Route::get('/form/layouts-vertical', [VerticalForm::class, 'index'])->name('form-layouts-vertical');
Route::get('/form/layouts-horizontal', [HorizontalForm::class, 'index'])->name('form-layouts-horizontal');
Route::get('/form/layouts-sticky', [StickyActions::class, 'index'])->name('form-layouts-sticky');

// form wizards
Route::get('/form/wizard-numbered', [FormWizardNumbered::class, 'index'])->name('form-wizard-numbered');
Route::get('/form/wizard-icons', [FormWizardIcons::class, 'index'])->name('form-wizard-icons');
Route::get('/form/validation', [Validation::class, 'index'])->name('form-validation');

// tables
Route::get('/tables/basic', [TablesBasic::class, 'index'])->name('tables-basic');
Route::get('/tables/datatables-basic', [DatatableBasic::class, 'index'])->name('tables-datatables-basic');
Route::get('/tables/datatables-advanced', [DatatableAdvanced::class, 'index'])->name('tables-datatables-advanced');
Route::get('/tables/datatables-extensions', [DatatableExtensions::class, 'index'])->name('tables-datatables-extensions');

// charts
Route::get('/charts/apex', [ApexCharts::class, 'index'])->name('charts-apex');
Route::get('/charts/chartjs', [ChartJs::class, 'index'])->name('charts-chartjs');

// maps
Route::get('/maps/leaflet', [Leaflet::class, 'index'])->name('maps-leaflet');

// laravel example
Route::get('/laravel/user-management', [UserManagement::class, 'UserManagement'])->name('laravel-example-user-management');
Route::resource('/user-list', UserManagement::class);
Route::get('/', [LoginCover::class, 'index'])->name('login');
Route::get('/logout', [LoginCover::class, 'logoutAuth'])->name('logout');
Route::get('/manager/logout', [LoginCover::class, 'managerLogout'])
    ->name('manager.logout');

Route::post('login-auth-form', [LoginCover::class, 'loginAuthForm'])->name('login-auth-form');

// forget password
Route::post('/forgot-password/send', [OTPVerifyController::class, 'sendOtp'])->name('auth.forgot-password.send');
Route::get('/verify-otp', [OTPVerifyController::class, 'showVerifyForm'])->name('auth.otp.verify');

Route::post('/verify-otp-submit', [OTPVerifyController::class, 'verifyOtp'])->name('auth.otp.submit');

Route::get('/reset-password-new', [OTPVerifyController::class, 'showNewPasswordForm'])->name('auth.password.reset.page');

Route::post('/reset-password-update', [OTPVerifyController::class, 'updatePassword'])->name('auth.password.update');
Route::post('/resend-otp', [OTPVerifyController::class, 'resendOtp'])->name('auth.otp.resend');

Route::get('/set-new-password/{email}', [OTPVerifyController::class, 'setNewPassword'])->name('auth.set.new.password');

Route::post('/set-password-generate', [OTPVerifyController::class, 'updateSetPassword'])
    ->name('auth.password.update.set.new');

// Public Intern Registration Routes - Start
Route::prefix('/intern-register')->group(function () {
    // Step 1 - Form display and data save
    Route::get('/step1', [InternPublicRegistrationController::class, 'step1'])->name('intern.register.step1');
    Route::post('/step1', [InternPublicRegistrationController::class, 'postStep1'])->name('intern.register.postStep1');
    
    // Step 2 - Assessment form display and answers save
    Route::get('/step2', [InternPublicRegistrationController::class, 'step2'])->name('intern.register.step2');
    Route::post('/step2', [InternPublicRegistrationController::class, 'postStep2'])->name('intern.register.postStep2');
    
    // Step 3 - Plan selection
    Route::get('/step3', [InternPublicRegistrationController::class, 'step3'])->name('intern.register.step3');
    
    // Complete registration
    Route::post('/complete', [InternPublicRegistrationController::class, 'complete'])->name('intern.register.complete');
    
    // Success page
    Route::get('/success', [InternPublicRegistrationController::class, 'success'])->name('intern.register.success');
});
// Public Intern Registration Routes - End


// ==========================================
// PROJECT CHAT ROUTES (Shared across all portals)
// ==========================================
Route::middleware(['auth:web,manager,intern'])->group(function () {
    
    // Communication center - Main chat hub for managers/supervisors/interns
    Route::get('/communication-center', [ChatController::class, 'index'])->name('chat.index');
    
    // Project chat view
    Route::get('/project-chat/{projectId}', [ChatController::class, 'show'])->name('chat.show');
    
    // Shared chat routes for project communication
    Route::get('/communication-center/{project_id}', [SupervisorProjectController::class, 'showChat'])
        ->name('supervisor.chat.show');

    // The Send Message action (The Form submit)
    Route::post('/communication-center/{project_id}/send', [SupervisorProjectController::class, 'sendMessage'])->name('supervisor.chat.send');
});




// Admin routes - Start
Route::prefix('/admin')->middleware(['validUser'])->group(function (){
    // User Management Routes
    Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/users/store', [UserController::class, 'store'])->name('admin.users.store');
    // Add an edit route for later
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');

// Dashboard Rotes
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard-admin');
Route::post('/send-broadcast', [DashboardController::class, 'sendTargetedBroadcast'])->name('admin.send-broadcast');


// All interns Routes
Route::get('/all-interns', [AllInternsController::class, 'allInterns'])->name('all-interns-admin');
Route::get('/all-interns/interview', [AllInternsController::class, 'interviewIntern'])->name('interview-admin');
Route::delete('/interns/{id}', [AllInternsController::class, 'removeIntern'])
    ->name('interns.destroy');
Route::get('/all-interns/contact', [AllInternsController::class, 'contactIntern'])->name('contact-admin');
Route::get('/all-interns/test', [AllInternsController::class, 'testIntern'])->name('test-admin');
Route::get('/all-interns/completed', [AllInternsController::class, 'completedIntern'])->name('completed-admin');
Route::get('/all-interns/active', [AllInternsController::class, 'activeIntern'])->name('active-admin');
Route::get('/view-profile-internee/{id}', [AllInternsController::class, 'viewProfileInternee'])->name('view.profile.internee.admin');
Route::post('update-intern', [AllInternsController::class, 'updateIntern'])->name('update.intern.admin');
Route::get('/interns-export/all', [AllInternsController::class, 'exportCSVAllInterns'])->name('all.interns.export.csv.admin');
Route::get('/interns-export/interview', [AllInternsController::class, 'exportCSVInterview'])->name('interview.interns.export.csv.admin');
Route::get('/interns-export/contact', [AllInternsController::class, 'exportCSVContact'])->name('contact.interns.export.csv.admin');

Route::get('/interns-export/test', [AllInternsController::class, 'exportCSVTest'])->name('test.interns.export.csv.admin');
Route::get('/interns-export/completed', [AllInternsController::class, 'exportCSVCompleted'])->name('completed.interns.export.csv.admin');
Route::get('/interns-export/active', [AllInternsController::class, 'exportCSVActive'])->name('active.interns.export.csv.admin');





// Intern Accounts Routes
Route::get('intern-accounts', [InternAccountsController::class, 'interAccounts'])->name('intern-accounts-admin');
Route::post('update-intern-account', [InternAccountsController::class, 'updateInternAccount'])->name('update-intern-account');
Route::get('/intern-view-profile-account/{id}', [InternAccountsController::class, 'InternViewProfileAccount'])->name('view.profile.interne.account.admin');
Route::get('/intern-accounts/export-csv', [InternAccountsController::class, 'exportInternAccountsCSV'])->name('export.intern.csv.admin');

 Route::get('/internship-registration', 
 [App\Http\Controllers\InternshipRegistrationController::class, 'step1'])->name('internship.step1');
 Route::post('/internship-registration/step2', 
 [App\Http\Controllers\InternshipRegistrationController::class, 'step2'])->name('internship.step2');
 Route::post('/internship-registration/step3', [App\Http\Controllers\InternshipRegistrationController::class, 'step3'])->name('internship.step3');

// Intern Projects
Route::get('intern-projects', [InternProjectsController::class, 'interProjects'])->name('intern-projects');
Route::post('update-intern-project', [InternProjectsController::class, 'updateInternProject'])->name('update.intern.project.admin');
Route::get('/intern-projects/export-csv', [InternProjectsController::class, 'exportInternProjectsCSV'])->name('export.intern.projects.csv.admin');


//Invoice routes
Route::get('/invoice',[InvoiceController::class,'invoice'])->name('invoice-page');
Route::get('/invoices/approval-queue',[InvoiceController::class,'approvalQueue'])->name('admin.invoices.approval-queue');
Route::get('/export-invoices', [InvoiceController::class, 'exportInvoiceCSV'])->name('admin.export-invoices');



// Managers
Route::get('managers', [ManagersController::class, 'managersData'])->name('managers');
Route::post('add-manager', [ManagersController::class, 'addManager'])->name('add-manager');
Route::put('/managers/update/{id}', [ManagersController::class, 'update'])->name('update-manager');
Route::get('show-active-technologies', [TechnologyController::class, 'activeTechnologies'])->name('active.technologies.admin');
Route::post('/manager-permissions/store', [ManagersController::class, 'storePermissions'])->name('manager.permissions.store');
Route::get('manager/{id}/permissions', [ManagersController::class, 'getManagerPermissions'])
    ->name('manager.permissions.get');
Route::get('/managers/export-csv', [ManagersController::class, 'downloadManagerCSV'])->name('managers.export.admin');
Route::get('/managers/permissions/{id}', [ManagersController::class, 'getManagerRoles'])->name('admin.managers.roles.permissions');
Route::get('/manager/resend-email/{id}', [ManagersController::class, 'resendEmail'])->name('manager.resend-email');



// Supervisors
Route::get('supervisors', [SupervisorsController::class, 'index'])->name('supervisors.admin');
Route::post('add-supervisor', [SupervisorsController::class, 'addSupervisor'])->name('add-supervisor.admin');
Route::put('/supervisor/update/{id}', [SupervisorsController::class, 'update'])->name('update-supervisor.admin');
Route::post('/supervisor-permissions/store', [SupervisorsController::class, 'storePermissions'])->name('supervisor.permissions.store');
Route::get('supervisor/{id}/permissions', [SupervisorsController::class, 'getSupervisorPermissions'])
    ->name('supervisor.permissions.get');
    // routes/web.php
Route::get('/supervisors/export-csv', [SupervisorsController::class, 'downloadSupervisorCSV'])->name('supervisors.export.admin');

// Technology
Route::get('technology', [TechnologyController::class, 'technologyData'])->name('technology');
Route::post('add-technology', [TechnologyController::class, 'addTechnology'])->name('add-technology');
Route::put('edit-technology', [TechnologyController::class, 'editTechnology'])->name('edit-technology');
Route::get('technology/download-csv', [TechnologyController::class, 'downloadTechnologiesCSV'])->name('download-technologies-csv.admin');

// Project Tasks
Route::get('project-tasks', [ProjectTaskController::class, 'index'])->name('project.tasks');
Route::post('update-project-task', [ProjectTaskController::class, 'updateProjecTask'])->name('update.project.task.admin');
Route::get('/export-project-tasks', [ProjectTaskController::class, 'exportProjectTasksCSV'])->name('export.project.tasks.admin');



// Intern Tasks
Route::get('intern-tasks', [InternTaskController::class, 'index'])->name('intern.tasks');
Route::post('update-intern-task', [InternTaskController::class, 'updateInternTask'])->name('update.intern.task.admin');
Route::get('/export-intern-tasks', [InternTaskController::class, 'exportInternTasksCSV'])->name('admin.export-intern-tasks');


// ADMIN LEAVE (ALL: intern + employee + supervisor)
// ✅ CORRECT (inside admin prefix)
Route::get('leave', [AdminLeaveController::class, 'index'])
    ->name('admin.leave');
    Route::get('/leaves/export', [AdminLeaveController::class, 'exportLeavesCSV'])->name('admin.leaves.export');

Route::post('employee-leave/approve/{id}', [AdminLeaveController::class, 'approveEmployee'])
    ->name('employee.leave.approve');

Route::post('employee-leave/reject/{id}', [AdminLeaveController::class, 'rejectEmployee'])
    ->name('employee.leave.reject');

Route::post('supervisor-leave/approve/{id}', [AdminLeaveController::class, 'approveSupervisor'])
    ->name('supervisor.leave.approve');

Route::post('supervisor-leave/reject/{id}', [AdminLeaveController::class, 'rejectSupervisor'])
    ->name('supervisor.leave.reject');
Route::post('intern-leave/approve/{id}', [AdminLeaveController::class, 'approveIntern'])
    ->name('intern.leave.approve');

Route::post('intern-leave/reject/{id}', [AdminLeaveController::class, 'rejectIntern'])
    ->name('intern.leave.reject');





// Univerity
Route::get('university', [UniversityController::class, 'index'])->name('university.admin');
Route::post(
    'university/store',
    [UniversityController::class, 'store']
)->name('add-university.admin');
Route::put('university/update', [UniversityController::class, 'update'])
    ->name('university.update.admin');
    // Route for exporting Universities
Route::get('/universities/export', [UniversityController::class, 'exportUniversityCSV'])->name('university.export.admin');
Route::delete('/university/{id}', [UniversityController::class, 'destroy'])->name('university.delete');

  // Settings Page
 Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
Route::post('/settings/update-profile', [SettingsController::class, 'updateAllSettings'])->name('admin.settings.update');
Route::post('/settings/change-password', [SettingsController::class, 'updatePassword'])->name('admin.password.update.admin');
Route::get('/accounts/export-csv', [AccountsController::class, 'exportAccountsCSV'])->name('accounts.export.csv.admin');



    // Accounts
Route::get('/accounts',[AccountsController::class,'index'])->name('accounts.admin');
Route::post('/accounts/add-transaction', [AccountsController::class, 'addTransaction'])
    ->name('add-transaction.admin');
Route::put('/transactions/update/{id}', [AccountsController::class, 'updateTransaction'])->name('update-transaction.admin');


//withdrw routes
Route::get('withdraw',[WithdrawAdminController::class,'index'])->name('admin.withdraw');
Route::get('withdraw/export-csv', [WithdrawAdminController::class, 'exportWithdrawCSV'])->name('admin.withdraw.export');
Route::any('withdraw/{id}/approve', [WithdrawAdminController::class, 'approve'])->name('admin.withdraw.approve');
Route::any('withdraw/{id}/reject', [WithdrawAdminController::class, 'reject'])->name('admin.withdraw.reject');
Route::put('withdraw/{id}/pay', [WithdrawAdminController::class, 'markPaid'])->name('admin.withdraw.pay');

// Feeback & complaint
Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.admin');
Route::post('/feedback/resolve/{id}', [FeedbackController::class, 'resolve'])
    ->name('feedback.resolve.admin');
    Route::get('/feedback/export', [FeedbackController::class, 'exportFeedbackCSV'])->name('feedback.export.admin');


// Knowledge base
Route::get('/knowledge-base', [KnowledgeBaseController::class, 'index'])->name('knowledge.base.admin');
Route::post('/knowledge/store', [KnowledgeBaseController::class, 'store'])
        ->name('knowledge.store.admin');

    Route::put('/knowledge/update/{id}', [KnowledgeBaseController::class, 'update'])
        ->name('knowledge.update.admin');

    Route::delete('/knowledge/delete/{id}', [KnowledgeBaseController::class, 'destroy'])
        ->name('knowledge.delete.admin');
        // routes/web.php
Route::get('/knowledge-base/export-csv', [KnowledgeBaseController::class, 'downloadKnowledgeBaseCSV'])->name('knowledge.base.export.admin');

    // ==================== ESCALATION ROUTES ====================
    Route::prefix('/escalations')->name('escalations.')->group(function() {
        Route::get('/', [App\Http\Controllers\EscalationController::class, 'index'])->name('index');
        Route::get('/statistics', [App\Http\Controllers\EscalationController::class, 'statistics'])->name('statistics');
        Route::get('/{id}', [App\Http\Controllers\EscalationController::class, 'show'])->name('show');
        Route::post('/{id}/resolve', [App\Http\Controllers\EscalationController::class, 'adminResolve'])->name('resolve');
        Route::post('/{id}/mark-notified', [App\Http\Controllers\EscalationController::class, 'markAdminNotified'])->name('mark-notified');
    });
    Route::get('/api/escalations/unnotified', [App\Http\Controllers\EscalationController::class, 'unnotifiedAdminEscalations'])->name('api.escalations.unnotified');

    

    // ==================== INVOICE APPROVAL ROUTES ====================
Route::get('/invoices/approval-queue', [App\Http\Controllers\InvoiceController::class, 'approvalQueue'])->name('admin.invoices.approval-queue');
Route::post('/invoices/approve/{id}', [App\Http\Controllers\InvoiceController::class, 'approveInvoice'])->name('admin.invoices.approve');
Route::post('/invoices/reject/{id}', [App\Http\Controllers\InvoiceController::class, 'rejectInvoice'])->name('admin.invoices.reject');

});
// Admin routes - End
// Admin routes - End



Route::prefix('/manager')->middleware(['validManager'])->group(function(){
    // Dashboard Route
    Route::get('/dashboard', [DashboardManagerController::class, 'index'])->name('manager.dashboard');

    // Curriculum Routes
    Route::get('/curriculum', [ManagerCurriculumController::class, 'index'])->name('manager.curriculum.index');
    Route::get('/curriculum/create', [ManagerCurriculumController::class, 'create'])->name('manager.curriculum.create');
    Route::post('/curriculum', [ManagerCurriculumController::class, 'store'])->name('manager.curriculum.store');
    Route::get('/curriculum/{id}', [ManagerCurriculumController::class, 'show'])->name('manager.curriculum.show');
    Route::get('/curriculum/{id}/edit', [ManagerCurriculumController::class, 'edit'])->name('manager.curriculum.edit');
    Route::put('/curriculum/{id}', [ManagerCurriculumController::class, 'update'])->name('manager.curriculum.update');
    Route::delete('/curriculum/{id}', [ManagerCurriculumController::class, 'destroy'])->name('manager.curriculum.destroy');
    
    // Curriculum Project Routes
    Route::post('/curriculum/{id}/project', [ManagerCurriculumProjectController::class, 'store'])->name('manager.curriculum.project.store');
    Route::put('/curriculum/{curriculum_id}/project/{project_id}', [ManagerCurriculumProjectController::class, 'update'])->name('manager.curriculum.project.update');
    Route::delete('/curriculum/{curriculum_id}/project/{project_id}', [ManagerCurriculumProjectController::class, 'destroy'])->name('manager.curriculum.project.destroy');

    // Tasks View Routes
    Route::get('/tasks', [TaskViewController::class, 'index'])->name('manager.tasks.index');
    Route::get('/tasks/{id}', [TaskViewController::class, 'show'])->name('manager.tasks.show');
    Route::get('/tasks/export', [TaskViewController::class, 'export'])->name('manager.tasks.export');

    // Revenue & Commission Tracking
    Route::get('/revenue', [RevenueController::class, 'index'])->name('manager.revenue');

    Route::get('/my-interns', [AllManagerInternController::class, 'myInterns']) ->name('manager.myInterns');
    Route::get('/my-interns/export', [AllManagerInternController::class, 'exportMyInternsCSV'])->name('manager.myInterns.export');

    
    // Active Interns submenu page
    Route::get('/all-interns', [AllManagerInternController::class, 'index']) ->name('manager.allInterns');
    Route::get('/all-interns/active', [AllManagerInternController::class, 'active'])->name('manager.activeInterns');
    Route::post('/update-intern-active-int', 
    [AllManagerInternController::class, 'updateInternActive'])
    ->name('update.intern.manager.active');
    Route::get('/manager/all-interns/active/export', [AllManagerInternController::class, 'exportActiveInterns'])
    ->name('manager.active.internee.export');

    Route::get('/international-interns', [InternationalInternsManagerController::class, 'index'])->name('manager.international.interns');
    Route::get('/manager/international-interns/export', [InternationalInternsManagerController::class, 'exportInternationalInterns'])
    ->name('manager.international.interns.export');

    Route::get('/all-interns/newInterns', [AllManagerInternController::class, 'newInterns'])->name('manager.newInterns');    
    Route::get('/all-interns/contact', [AllManagerInternController::class, 'contactWith'])->name('manager.contactWith');    
    Route::get('/all-interns/interview', [AllManagerInternController::class, 'interview'])->name('manager.interview');    

    // Offer Letter Route (corrected)
    Route::get('/offer-letter-template', [OfferLetterTemplateController::class, 'index'])->name('manager.offer.letter.template');
    Route::post('/offer-letter-template-create', [OfferLetterTemplateController::class, 'store'])->name('manager.offer.letter.template.create');
    Route::put('/manager/offer-letter-template-update/{id}', [OfferLetterTemplateController::class, 'update'])->name('manager.offer.letter.template.update');
Route::delete('/offer-letter-template/delete/{id}', [OfferLetterTemplateController::class, 'destroy'])
     ->name('manager.offer.letter.template.delete');

        Route::get('/offer-letter-request', [OfferLetterRequestController::class, 'index'])->name('manager.offer.letter.request');
Route::get('/offer-letter-request/export', [OfferLetterRequestController::class, 'exportCSV'])
         ->name('manager.offer-letter.export');
        Route::post('/offer-letter-request/update-status', [OfferLetterRequestController::class, 'updateStatus'])
         ->name('manager.offer-letter.update-status');

Route::get('/get-template-preview/{templateId}/{internId}', [OfferLetterRequestController::class, 'getTemplatePreview'])
         ->name('manager.get.template.preview');
Route::post('/send-offer-letter', [OfferLetterRequestController::class, 'sendOfferLetter'])->name('send.offer.letter');

Route::get('/download-offer-letter-pdf', [OfferLetterRequestController::class, 'downloadOfferLetterPdf'])->name('manager.download.pdf');

    Route::get('/certificate-templates', [CertificateController::class, 'templates'])->name('manager.certificate.templates');
    Route::post('/certificate-templates/create', [CertificateController::class, 'storeTemplate'])->name('manager.certificate.template.create');
    Route::put('/certificate-templates/update/{id}', [CertificateController::class, 'updateTemplate'])->name('manager.certificate.template.update');
    Route::delete('/certificate-templates/delete/{id}', [CertificateController::class, 'destroyTemplate'])->name('manager.certificate.template.delete');
    Route::get('/certificate-templates/preview/{id}', [CertificateController::class, 'previewTemplate'])->name('manager.certificate.template.preview');

    Route::get('/certificate-requests', [CertificateController::class, 'requests'])->name('manager.certificate.requests');
    Route::post('/certificate-requests/submit', [CertificateController::class, 'submitRequest'])->name('manager.certificate.request.submit');
    Route::post('/certificate-requests/update-status', [CertificateController::class, 'updateRequestStatus'])->name('manager.certificate.request.update-status');
    Route::get('/certificate-requests/download/{id}', [CertificateController::class, 'downloadCertificate'])->name('manager.certificate.request.download');


    Route::get('/remainingamount', [RemainingAmountController::class, 'index'])->name('manager.remainingamount');


    Route::get('/active-interns/export', [AllManagerInternController::class, 'exportActiveInternsCSV'])->name('manager.active.export');



    Route::patch('/manager/interns/remove/{id}', [AllManagerInternController::class, 'remove'])->name('manager.interns.remove');
    Route::patch('/manager/intern-active/remove/{id}', 
    [AllManagerInternController::class, 'removeInternAccActive'])
    ->name('remove.internActiveAcc.manager');
     Route::post('/interns/update', [AllManagerInternController::class, 'updateStatus'])->name('update.intern.manager');
    
Route::get('/all-interns/newInterns', [AllManagerInternController::class, 'newInterns'])->name('manager.newInterns');  
Route::get('/newInterns/export', [AllManagerInternController::class, 'exportNewInternsCSV'])->name('manager.newInterns.export');


Route::get('/all-interns/contact', [AllManagerInternController::class, 'contactWith'])->name('manager.contactWith');   
Route::get('/contact-with/export', [AllManagerInternController::class, 'exportContactWith'])->name('manager.contactWith.export');

Route::get('/all-interns/test', [AllManagerInternController::class, 'test'])->name('manager.test'); 
Route::get('/test-interns/export', [AllManagerInternController::class, 'exportTestCSV'])->name('manager.test.export');


Route::get('/all-interns/completed', [AllManagerInternController::class, 'completed'])->name('manager.completed'); 
Route::get('/completed-interns/export', [AllManagerInternController::class, 'exportCompletedCSV'])->name('manager.completed.export');





//Payment Receipt Routes

Route::get('/payment-receipt',[PaymentReceiptController::class,'index'])->name('manager.payment-receipt');



Route::get('/profile-settings', [ProfileSettingsController::class, 'index'])->name('manager.profile.settings');
Route::post('profile-settings/update',
            [ProfileSettingsController::class, 'update']
        )->name('manager.profile.update');
Route::post('password/update', [ProfileSettingsController::class, 'updatePassword'])
        ->name('manager.password.update');


Route::get('/knowledge-base', [ManagerKnowledgeBaseController::class, 'index'])->name('manager.knowledge.base');
Route::get('/knowledge-base/export',
    [ManagerKnowledgeBaseController::class, 'exportKnowledgeBaseCSV']
)->name('manager.knowledge-base.export');

// Invoice Routes
Route::get('/invoices', [ManagerInvoiceController::class, 'dashboard'])->name('invoices.dashboard');
Route::get('/invoice/create', [ManagerInvoiceController::class, 'create'])->name('invoices.create');
Route::post('/invoice', [ManagerInvoiceController::class, 'store'])->name('invoices.store');
Route::get('/invoice/{id}', [ManagerInvoiceController::class, 'show'])->name('invoices.view');
Route::get('/invoice/{id}/payment', [ManagerInvoiceController::class, 'paymentForm'])->name('invoices.payment');
Route::post('/invoice/{id}/record-payment', [ManagerInvoiceController::class, 'recordPayment'])->name('invoices.record-payment');
Route::post('/invoice/{id}/update-due-date', [ManagerInvoiceController::class, 'updateDueDate'])->name('invoices.update-due-date');
Route::get('/invoice-export', [ManagerInvoiceController::class, 'export'])->name('invoices.export');
Route::get('/invoice/{id}/pdf', [ManagerInvoiceController::class, 'generatePDF'])->name('invoices.pdf');
Route::get('/invoice/{id}/view-pdf', [ManagerInvoiceController::class, 'viewPDF'])->name('invoices.view-pdf');

// Communication/Messaging Routes
Route::get('/communication', [CommunicationController::class, 'index'])->name('manager.communication');
Route::post('/send-message', [CommunicationController::class, 'sendMessage'])->name('manager.send.message');

// Attendance Routes
Route::get('/attendance/manage', [ManagerAttendanceController::class, 'attendanceManagement'])->name('manager.attendance.manage');
Route::get('/attendance', [ManagerAttendanceController::class, 'attendanceManagement'])->name('manager.attendance'); // Points to new unified view
Route::get('/attendance/interns', [ManagerAttendanceController::class, 'internAttendance'])->name('manager.attendance.interns');
Route::get('/attendance-calendar', [ManagerAttendanceController::class, 'attendanceCalendar'])->name('manager.attendance.calendar');

// Leave Management Routes (Intern & Supervisor)
Route::get('/leaves/intern', [ManagerLeaveController::class, 'intern'])->name('manager.leaves.intern');
Route::get('/leaves/supervisor', [ManagerLeaveController::class, 'supervisor'])->name('manager.leaves.supervisor');
Route::post('/leaves/intern/approve/{id}', [ManagerLeaveController::class, 'approve'])->name('manager.leave.approve');
Route::post('/leaves/intern/reject/{id}', [ManagerLeaveController::class, 'reject'])->name('manager.leave.reject');
Route::post('/supervisor-leave/approve/{id}', [ManagerLeaveController::class, 'supervisorapprove'])->name('manager.leave.supervisor.approve');
Route::post('/supervisor-leave/reject/{id}', [ManagerLeaveController::class, 'supervisorreject'])->name('manager.leave.supervisor.reject');

// Supervisor Routes
Route::get('/supervisors', [Supervisorcontroller::class, 'index'])->name('manager.supervisors');
Route::get('/withdraw', [WithdrawManagerController::class, 'index'])
    ->name('manager.withdraw');

Route::post('/withdraw/store', [WithdrawManagerController::class, 'store'])
    ->name('manager.withdraw.store');
// LIST PAGE
Route::get('/performance-analytics', 
    [InternPerformanceController::class, 'index']
)->name('performance.analytics');

// DETAIL PAGE
Route::get('/performance-analytics/{id}', 
    [InternPerformanceController::class, 'show']
)->name('performance.detail');

// Escalation Routes (Manager View)
Route::get('/escalations', [EscalationController::class, 'managerEscalations'])->name('manager.escalations');
Route::get('/escalations/{id}', [EscalationController::class, 'show'])->name('manager.escalations.show');
Route::post('/escalations/{id}/resolve', [EscalationController::class, 'resolve'])->name('manager.escalations.resolve');

});



Route::prefix('/supervisor')->middleware(['validSupervisor'])->group(function () {

// The route to display the chat room for a specific project
// Route::get('/projects/chat/{project_id}', [SupervisorProjectController::class, 'showChat'])
//         ->name('supervisor.projects.chat.room');

//     // ADD THIS: The route for the "Initiate Chat" form
//     Route::post('/projects/chat/initiate/{project_id}', [SupervisorProjectController::class, 'initiateChat'])
//         ->name('supervisor.projects.chat.initiate');

        //ends chat routes

    Route::get('/dashboard', [DashboardSupervisorController::class, 'index'])->name('supervisor.dashboard');

    Route::get('/my-interns', [SupervisorInternController::class, 'myInterns'])->name('supervisor.myInterns');
    Route::get('/all-interns/active', [SupervisorInternController::class, 'active'])->name('supervisor.activeInterns');
    Route::get('/all-interns/newInterns', [SupervisorInternController::class, 'newInterns'])->name('supervisor.newInterns');
    Route::get('/all-interns/contact', [SupervisorInternController::class, 'contactWith'])->name('supervisor.contactWith');
    Route::get('/all-interns/test', [SupervisorInternController::class, 'test'])->name('supervisor.test');
    Route::get('/all-interns/completed', [SupervisorInternController::class, 'completed'])->name('supervisor.completed');
    Route::get('/view-intern/{id}', [SupervisorInternController::class, 'show'])->name('supervisor.viewIntern');
    Route::get('/progress-monitoring', [SupervisorInternController::class, 'progressMonitoring'])->name('supervisor.progressMonitoring');

    Route::get('/projects', [SupervisorProjectController::class, 'index'])->name('supervisor.projects');
    

    // 1. The List Page (GET)
    Route::get('/projects', [SupervisorProjectController::class, 'index'])->name('supervisor.projects');

    // 2. The Store Action (POST) - This matches the form I just sent you
    Route::post('/projects/save', [SupervisorProjectController::class, 'store'])->name('supervisor.projects.store');

    // 3. The Initiate Chat Action (POST)
    Route::post('/projects/chat-initiate/{project_id}', [SupervisorProjectController::class, 'initiateChat'])->name('supervisor.projects.chat.initiate');
    // 4. The Chat Room (GET)
    // Route::get('/projects/chat-room/{project_id}', [SupervisorProjectController::class, 'showChat'])
    // ->name('chat.show');

// changing for now show shat
    // Route::get('/projects/chat/{project_id}', [SupervisorProjectController::class, 'showChat'])->name('supervisor.projects.chat.room');
    
    
    
    // Route::post('/supervisor/projects/store', [SupervisorProjectController::class, 'store'])->name('supervisor.projects.store');
    Route::get('/supervisor/projects/{project_id}/tasks', [SupervisorProjectController::class, 'tasks'])->name('supervisor.projects.tasks');
    Route::post('/supervisor/projects/{project_id}/tasks/store', [SupervisorProjectController::class, 'storeTask'])->name('supervisor.projects.tasks.store');
    Route::post('/supervisor/projects/{project_id}/tasks/load-curriculum', [SupervisorProjectController::class, 'loadCurriculum'])->name('supervisor.projects.tasks.loadCurriculum');
    Route::get('/supervisor/projects/{project_id}/tasks/{task_id}/edit', [SupervisorProjectController::class, 'editTask'])->name('supervisor.projects.tasks.edit');
    Route::post('/supervisor/projects/{project_id}/tasks/{task_id}/update', [SupervisorProjectController::class, 'updateTask'])->name('supervisor.projects.tasks.update');
    Route::delete('/supervisor/projects/{project_id}/tasks/{task_id}/delete', [SupervisorProjectController::class, 'deleteTask'])->name('supervisor.projects.tasks.delete');

    // Project CRUD
    Route::get('/projects/edit/{id}', [SupervisorProjectController::class, 'edit'])->name('supervisor.projects.edit');
    Route::post('/projects/update/{id}', [SupervisorProjectController::class, 'update'])->name('supervisor.projects.update');
    Route::delete('/projects/delete/{id}', [SupervisorProjectController::class, 'destroy'])->name('supervisor.projects.delete');
    



    Route::get('/attendance', [SupervisorAttendanceController::class, 'index'])->name('supervisor.attendance');
    Route::get('/leaves', [SupervisorLeaveController::class, 'index'])->name('supervisor.leaves');
    Route::get('/feedback', [SupervisorFeedbackController::class, 'index'])->name('supervisor.feedback');
    Route::post('/feedback/store', [SupervisorFeedbackController::class, 'store'])->name('supervisor.feedback.store');

    Route::get('/profile-settings', [SupervisorProfileController::class, 'index'])->name('supervisor.profile.settings');
    Route::get('/knowledge-base', [SupervisorKnowledgeBaseController::class, 'index'])->name('supervisor.knowledge.base');

    // General Tasks
    Route::get('/tasks', [SupervisorTaskController::class, 'index'])->name('supervisor.tasks.index');
    Route::get('/tasks/kanban', [SupervisorTaskController::class, 'kanban'])->name('supervisor.tasks.kanban');
    Route::get('/tasks/fetch-kanban-ajax', [SupervisorTaskController::class, 'fetchKanbanTasksAjax'])->name('supervisor.tasks.fetchKanbanAjax');
    Route::post('/tasks/update-status-ajax', [SupervisorTaskController::class, 'updateStatusAjax'])->name('supervisor.tasks.updateStatusAjax');
    Route::get('/tasks/create', [SupervisorTaskController::class, 'create'])->name('supervisor.tasks.create');
    Route::post('/tasks/store', [SupervisorTaskController::class, 'store'])->name('supervisor.tasks.store');
    Route::get('/tasks/review/{id}', [SupervisorTaskController::class, 'review'])->name('supervisor.tasks.review');
    Route::post('/tasks/update/{id}', [SupervisorTaskController::class, 'update'])->name('supervisor.tasks.update');

    // Task CRUD (Edit/Delete)
    Route::get('/tasks/edit/{id}', [SupervisorTaskController::class, 'edit'])->name('supervisor.tasks.edit');
    Route::post('/tasks/update-details/{id}', [SupervisorTaskController::class, 'updateDetails'])->name('supervisor.tasks.updateDetails');
    Route::delete('/tasks/delete/{id}', [SupervisorTaskController::class, 'destroy'])->name('supervisor.tasks.delete');

    // Evaluations
    Route::get('/evaluations', [SupervisorEvaluationController::class, 'index'])->name('supervisor.evaluations.index');
    Route::get('/evaluations/create/{eti_id}', [SupervisorEvaluationController::class, 'create'])->name('supervisor.evaluations.create');
    Route::post('/evaluations/store', [SupervisorEvaluationController::class, 'store'])->name('supervisor.evaluations.store');

    // Evaluation CRUD
    Route::get('/evaluations/edit/{id}', [SupervisorEvaluationController::class, 'edit'])->name('supervisor.evaluations.edit');
    Route::post('/evaluations/update/{id}', [SupervisorEvaluationController::class, 'update'])->name('supervisor.evaluations.update');
    Route::delete('/evaluations/delete/{id}', [SupervisorEvaluationController::class, 'destroy'])->name('supervisor.evaluations.delete');
});


Route::prefix('/intern')->middleware(['validIntern'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [InternDashboardController::class, 'index'])->name('intern.dashboard');
    Route::post('/logout', [LoginCover::class, 'internLogout'])->name('intern.logout');

    // Profile Routes
    Route::get('/profile', [InternProfileController::class, 'index'])->name('intern.profile');
    Route::get('/profile/edit', [InternProfileController::class, 'edit'])->name('intern.profile.edit');
Route::post('/profile/update', [InternProfileController::class, 'update'])->name('intern.profile.update');    Route::post('/update-profile-image', [InternProfileController::class, 'updateProfileImage'])->name('intern.update-profile-image');
    Route::get('/profile/public/{identifier?}', [InternProfileController::class, 'publicProfile'])->name('intern.profile.public');
    Route::get('/portfolio', [InternProfileController::class, 'portfolio'])->name('intern.portfolio');

    // Settings Routes
    Route::get('/settings', [InternSettingsController::class, 'index'])->name('intern.settings');
    Route::post('/settings/update', [InternSettingsController::class, 'updateSettings'])->name('intern.settings.update');
    Route::post('/settings/update-password', [InternSettingsController::class, 'updatePassword'])->name('intern.settings.update-password');

    // Resources Routes
    Route::get('/resources', [InternResourceController::class, 'index'])->name('intern.resources');
    Route::get('/resources/{id}', [InternResourceController::class, 'show'])->name('intern.resources.show');
    Route::post('/resources/{id}/download', [InternResourceController::class, 'download'])->name('intern.resources.download');
    Route::post('/resources/{id}/complete', [InternResourceController::class, 'markComplete'])->name('intern.resources.complete');

    // Projects Routes
    Route::get('/projects', [InternProjectController::class, 'index'])->name('intern.projects');
    Route::get('/projects/{id}', [InternProjectController::class, 'show'])->name('intern.projects.show');

    // Tasks Routes
    Route::get('/tasks', [InternPortalTaskController::class, 'index'])->name('intern.tasks');
    Route::get('/tasks/{id}', [InternPortalTaskController::class, 'show'])->name('intern.tasks.show');
    Route::post('/tasks/{id}/submit', [InternPortalTaskController::class, 'submit'])->name('intern.tasks.submit');

    // Leave Routes
    Route::get('/leave', [InternLeaveController::class, 'index'])->name('intern.leave');
    Route::post('/leave/request', [InternLeaveController::class, 'requestLeave'])->name('intern.leave.request');

    // Invoices Routes
    Route::get('/invoices', [InternInvoiceController::class, 'index'])->name('intern.invoices');
    Route::get('/invoices/{id}', [InternInvoiceController::class, 'show'])->name('intern.invoices.show');
Route::post('/invoices', [InternInvoiceController::class, 'store'])
    ->name('intern.invoices.store');
    // Feedback Routes
    Route::get('/feedback', [InternFeedbackController::class, 'index'])->name('intern.feedback');
    Route::post('/feedback/submit', [InternFeedbackController::class, 'submitFeedback'])->name('intern.feedback.submit');

    // Certificates Routes
    Route::get('/certificates', [InternCertificateController::class, 'index'])->name('intern.certificates');
    Route::post('/certificates/request', [InternCertificateController::class, 'requestCertificate'])->name('intern.certificates.request');
    Route::get('/certificates/download/{id}', [InternCertificateController::class, 'downloadCertificate'])->name('intern.certificates.download');

    // Attendance Routes
    Route::get('/attendance', [InternAttendanceController::class, 'index'])->name('intern.attendance');
    Route::post('/attendance/checkin', [InternAttendanceController::class, 'checkIn'])->name('intern.attendance.checkin');
    Route::post('/attendance/checkout', [InternAttendanceController::class, 'checkOut'])->name('intern.attendance.checkout');

    // Offer Letter Routes
    Route::get('/offer-letter', [InternOfferLetterController::class, 'index'])->name('intern.offer-letter');
    Route::get('/offer-letter/download', [InternOfferLetterController::class, 'download'])->name('intern.offer-letter.download');
});

   
Route::fallback(function (){
    return view('pages.pageNotFound');
});