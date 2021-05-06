<?php
/**
 * @filesource modules/inventory/controllers/customer.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Inventory\Customer;

use Gcms\Login;
use Kotchasan\Html;
use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * module=inventory-customer
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Gcms\Controller
{
    /**
     * เพิ่ม แก้ไข ลูกค้า.
     *
     * @param Request $request
     *
     * @return string
     */
    public function render(Request $request)
    {
        // ข้อความ title bar
        $this->title = Language::trans('{LNG_Supplier}/{LNG_Customer}');
        // เลือกเมนู
        $this->menu = 'customer';
        // สามารถดูรายชื่อลูกค้าได้
        if ($login = Login::checkPermission(Login::isMember(), array('can_inventory_order', 'can_inventory_order', 'can_manage_inventory'))) {
            // อ่านข้อมูลลูกค้า
            $customer = \Inventory\Customer\Model::get($request->request('id')->toInt());
            if ($customer) {
                // ข้อความ title bar
                $title = Language::get($customer['id'] == 0 ? 'Add New' : 'Details of');
                $this->title = $title.' '.$this->title;
                // แสดงผล
                $section = Html::create('section', array(
                    'class' => 'content_bg',
                ));
                // breadcrumbs
                $breadcrumbs = $section->add('div', array(
                    'class' => 'breadcrumbs',
                ));
                $ul = $breadcrumbs->add('ul');
                $ul->appendChild('<li><a href="index.php" class="icon-home">{LNG_Home}</a></li>');
                $ul->appendChild('<li><a href="index.php?module=customers">{LNG_Customer}</a></li>');
                $ul->appendChild('<li><span>'.$title.'</span></li>');
                $section->add('header', array(
                    'innerHTML' => '<h2 class="icon-customer">'.$this->title.'</h2>',
                ));
                // แสดงฟอร์ม
                $section->appendChild(createClass('Inventory\Customer\View')->render($customer, $login));
                return $section->render();
            }
        }
        // 404
        return \Index\Error\Controller::execute($this, $request->getUri());
    }

    /**
     * แสดงฟอร์ม Modal
     *
     * @param Request $request
     */
    public function showModal(Request $request)
    {
        // สมาชิก
        if ($login = Login::isMember()) {
            // typ = 1 คือ Supplier ถ้าไม่ใช่ คือ Customer
            $type = Language::get($request->post('typ')->toInt() == 1 ? 'Supplier' : 'Customer');
            // View
            $view = new \Gcms\View();
            // เพิ่ม
            $customer = \Inventory\Customer\Model::get(0);
            // แสดงผลฟอร์ม
            echo $view->renderHTML(createClass('Inventory\Customer\View')->render($customer, $login, $type));
        }
    }
}
