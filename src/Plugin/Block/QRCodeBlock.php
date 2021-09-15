<?php

namespace Drupal\endriod_qr_code\Plugin\Block;

use Drupal\Core\Block\BlockBase;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;

/**
 * Provides a 'endriod_qr_code' block.
 *
 * @Block(
 *   id = "endriod_qr_code_block",
 *   admin_label = @Translation("Endriod QR Code block"),
 *   category = @Translation("Custom Block")
 * )
 */
class QRCodeBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $node = \Drupal::routeMatch()->getParameter('node');
    $nid = $node->get('nid')->getValue()[0]['value'];
    $link = $node->get('field_link')->getValue()[0]['uri'];

    $module_handler = \Drupal::service('module_handler');
    $module_path = $module_handler->getModule('endriod_qr_code')->getPath();

    $writer = new PngWriter();

    // Create QR code.
    $qrCode = QrCode::create($link)
      ->setEncoding(new Encoding('UTF-8'))
      ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
      ->setSize(300)
      ->setMargin(10)
      ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
      ->setForegroundColor(new Color(0, 0, 0))
      ->setBackgroundColor(new Color(255, 255, 255));

    $result = $writer->write($qrCode, NULL, NULL);
    $result->saveToFile($module_path . '/images/qrcode-' . $nid . '.png');

    return [
      '#type' => 'markup',
      '#markup' => '<p>To Purchase this product on our app to avail execlusive app-only</p><img src="' . file_create_url($module_path . '/images/qrcode-' . $nid . '.png') . '" alt="Purchase QRCode" width="220" height="185">',
    ];
  }

}
