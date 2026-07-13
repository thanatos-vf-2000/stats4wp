<?php
/**
 * @package STATS4WPPlugin
 * @version 1.6.0
 *
 * Description: HTML body of the weekly stats email report.
 *
 * Expects a $data array with keys: from, to, visitors, visits, top_pages,
 * top_countries, site_name, site_url, dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title><?php echo esc_html( $data['site_name'] ); ?></title>
</head>
<body style="margin:0;padding:0;background:#f4f5f7;font-family:Arial, Helvetica, sans-serif;color:#32373c;">
	<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f5f7;padding:24px 0;">
		<tr>
			<td align="center">
				<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:6px;overflow:hidden;">
					<tr>
						<td style="background:#23282d;padding:20px 24px;">
							<a href="<?php echo esc_url( $data['site_url'] ); ?>" style="color:#ffffff;text-decoration:none;font-size:18px;font-weight:bold;">
								<?php echo esc_html( $data['site_name'] ); ?>
							</a>
							<div style="color:#a7aaad;font-size:13px;margin-top:4px;">
								<?php
								printf(
									/* translators: 1: start date, 2: end date */
									esc_html__( 'Weekly stats report - %1$s to %2$s', 'stats4wp' ),
									esc_html( $data['from'] ),
									esc_html( $data['to'] )
								);
								?>
							</div>
						</td>
					</tr>
					<tr>
						<td style="padding:24px;">
							<table role="presentation" width="100%" cellpadding="0" cellspacing="0">
								<tr>
									<td width="50%" style="padding:12px;background:#f0f6fc;border-radius:4px;text-align:center;">
										<div style="font-size:28px;font-weight:bold;color:#2271b1;"><?php echo esc_html( number_format_i18n( $data['visitors'] ) ); ?></div>
										<div style="font-size:13px;color:#646970;"><?php esc_html_e( 'Visitors', 'stats4wp' ); ?></div>
									</td>
									<td width="12"></td>
									<td width="50%" style="padding:12px;background:#f0f6fc;border-radius:4px;text-align:center;">
										<div style="font-size:28px;font-weight:bold;color:#2271b1;"><?php echo esc_html( number_format_i18n( $data['visits'] ) ); ?></div>
										<div style="font-size:13px;color:#646970;"><?php esc_html_e( 'Visits', 'stats4wp' ); ?></div>
									</td>
								</tr>
							</table>

							<h3 style="margin:24px 0 8px;font-size:15px;color:#1d2327;"><?php esc_html_e( 'Top 5 pages', 'stats4wp' ); ?></h3>
							<?php if ( empty( $data['top_pages'] ) ) : ?>
								<p style="font-size:13px;color:#646970;"><?php esc_html_e( 'No page views recorded for this period.', 'stats4wp' ); ?></p>
							<?php else : ?>
								<table role="presentation" width="100%" cellpadding="6" cellspacing="0" style="font-size:13px;border-collapse:collapse;">
									<?php foreach ( $data['top_pages'] as $page ) : ?>
										<tr style="border-bottom:1px solid #f0f0f1;">
											<td style="color:#1d2327;word-break:break-all;"><?php echo esc_html( $page->uri ); ?></td>
											<td width="70" align="right" style="color:#646970;"><?php echo esc_html( number_format_i18n( $page->hits ) ); ?></td>
										</tr>
									<?php endforeach; ?>
								</table>
							<?php endif; ?>

							<h3 style="margin:24px 0 8px;font-size:15px;color:#1d2327;"><?php esc_html_e( 'Top 5 countries', 'stats4wp' ); ?></h3>
							<?php if ( empty( $data['top_countries'] ) ) : ?>
								<p style="font-size:13px;color:#646970;"><?php esc_html_e( 'No location data recorded for this period.', 'stats4wp' ); ?></p>
							<?php else : ?>
								<table role="presentation" width="100%" cellpadding="6" cellspacing="0" style="font-size:13px;border-collapse:collapse;">
									<?php foreach ( $data['top_countries'] as $country ) : ?>
										<tr style="border-bottom:1px solid #f0f0f1;">
											<td style="color:#1d2327;"><?php echo esc_html( $country->location ); ?></td>
											<td width="70" align="right" style="color:#646970;"><?php echo esc_html( number_format_i18n( $country->nb ) ); ?></td>
										</tr>
									<?php endforeach; ?>
								</table>
							<?php endif; ?>

							<p style="margin-top:24px;">
								<a href="<?php echo esc_url( $data['dashboard'] ); ?>" style="display:inline-block;background:#2271b1;color:#ffffff;text-decoration:none;padding:10px 18px;border-radius:4px;font-size:13px;">
									<?php esc_html_e( 'View full dashboard', 'stats4wp' ); ?>
								</a>
							</p>
						</td>
					</tr>
					<tr>
						<td style="padding:16px 24px;background:#f6f7f7;font-size:11px;color:#8c8f94;text-align:center;">
							<?php esc_html_e( 'Sent automatically by stats4wp. You can disable this report from the plugin settings.', 'stats4wp' ); ?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>
</html>
