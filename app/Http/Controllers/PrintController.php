<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;


class PrintController extends Controller
{
    public function print(){
        $connector = new FilePrintConnector("php://stdout");

        /* Information for the receipt */
        $items = array(
            new item("1 Hamburguesa", "4.00"),
            new item("2 Coca colas", "3.50"),
            new item("1 lomo", "1.00"),
            new item("1 salchipapas", "4.45"),
        );
        $subtotal = new item('Subtotal', '12.00');
        $tax = new item('Descuento', '1.00');
        $total = new item('Total', '11.00', true);
        /* Date is kept the same for testing */
        $date = date('l jS \of F Y h:i:s A');
        // $date = "Monday 6th of April 2015 02:56:25 PM";

        /* Start the printer */
        // $logo = EscposImage::load(url('image/logo.png'), false);
        $printer = new Printer($connector);

        /* Print top logo */
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        // $printer->graphics($logo);

        /* Name of shop */
        $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer->text("Pollos Don Carlitos\n");
        $printer->selectPrintMode();
        $printer->text("Ticket No. 42.\n");
        $printer->feed();

        /* Title of receipt */
        $printer->setEmphasis(true);
        $printer->text("Detalle de venta\n");
        $printer->setEmphasis(false);

        /* Items */
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->setEmphasis(true);
        $printer->text(new item('Productos', 'Bs.'));
        $printer->setEmphasis(false);
        foreach ($items as $item) {
            $printer->text($item);
        }
        $printer->setEmphasis(true);
        $printer->text($subtotal);
        $printer->setEmphasis(false);
        $printer->feed();

        /* Tax and total */
        $printer->text($tax);
        $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer->text($total);
        $printer->selectPrintMode();

        /* Footer */
        $printer->feed(2);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("Gracias por su preferencia!!!\n");
        $printer->text("Gerente.rest\n");
        $printer->feed(2);
        $printer->text($date . "\n");

        /* Cut the receipt and open the cash drawer */
        $printer->cut();
        $printer->pulse();

        $printer->close();
    }
}

/* A wrapper to do organise item names & prices into columns */
class item
{
    private $name;
    private $price;
    private $dollarSign;

    public function __construct($name = '', $price = '', $dollarSign = false)
    {
        $this->name = $name;
        $this->price = $price;
        $this->dollarSign = $dollarSign;
    }
    
    public function __toString()
    {
        $rightCols = 10;
        $leftCols = 38;
        if ($this->dollarSign) {
            $leftCols = $leftCols / 2 - $rightCols / 2;
        }
        $left = str_pad($this->name, $leftCols) ;
        
        $sign = ($this->dollarSign ? '$ ' : '');
        $right = str_pad($sign . $this->price, $rightCols, ' ', STR_PAD_LEFT);
        return "$left$right\n";
    }
}
