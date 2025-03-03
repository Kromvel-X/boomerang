import { useBlockProps, RichText } from '@wordpress/block-editor';

export default function save({ attributes }) {
    const { title, buttonText, bonuses } = attributes;
	
    return (
		<section {...useBlockProps.save({ className: 'sctn hero-banner' })}>
			<div className="hero-banner__box">
				<div className="container container--small">
					<header className="hero-banner__header">
						<RichText.Content tagName="h1" className="hero-banner__title" value={title} />
					</header>

					{/* Форма */}
					<form className="bonus-pseudo-form" action="#" method="POST">
						<div className="bonus-list">
							{/* Первый бонус (index === 0) рендерится отдельно */}
							{bonuses.length > 0 && (
								<div key={0} className="bonus-list__item dropdown-btn select-dropdown-bonus" data-icon="bonus1" data-dropdown="bonuses">
									<RichText.Content tagName="p" className="bonus-list__name" value={bonuses[0].name} />
									<RichText.Content tagName="p" className="bonus-list__offer" value={bonuses[0].description} />
									<span className="arrow_button"></span>
								</div>
							)}

							{/* Все остальные бонусы (index > 0) попадают внутрь #bonuses */}
							{bonuses.length > 1 && (
								<div id="bonuses" className="dropdown-content bonus-list__dropdown">
									<div className="bonus-list__box">
										{bonuses.slice(1).map((bonus, index) => (
											<div key={index + 1} className="bonus-list__item dropdown-item lazy_image_bc" data-icon={`bonus${index + 2}`}>
												<RichText.Content tagName="p" className="bonus-list__name" value={bonus.name} />
												<RichText.Content tagName="p" className="bonus-list__offer" value={bonus.description} />
											</div>
										))}
									</div>
								</div>
							)}
						</div>

						{/* Поля формы */}
						<div className="row bonus-pseudo-form__fields">
							<div className="col bonus-pseudo-form__field">
								<label htmlFor="deposit-amount">Deposit Amount</label>
								<input type="number" id="deposit-amount" className="input" name="deposit" min="0" value="60" />
							</div>
							<div className="col bonus-pseudo-form__field">
								<label htmlFor="currency">Currency</label>
								<input type="hidden" name="currency" id="currency-value" value="EUR"></input>
								<div className="bonus-pseudo-form__select dropdown-btn pos-r select-dropdown" data-dropdown="currency">
									<span className="dropdown-btn__text">EUR</span>
									<span className="arrow_button"></span>
									<ul id="currency" className="dropdown-content">
										<li className="dropdown-item selected">EUR</li>
										<li className="dropdown-item">USD</li>
										<li className="dropdown-item">RUB</li>
									</ul>
								</div>
							</div>
						</div>

						{/* Кнопка отправки формы */}
						<button type="submit" className="button bonus-pseudo-form__button">
							{buttonText}
						</button>
					</form>

					<div className="ps">
						<span className="ps__logo visa"></span>
						<span className="ps__logo mastercard"></span>
						<span className="ps__logo bt"></span>
						<span className="ps__logo ethereum"></span>
						<span className="ps__logo tether"></span>
						<span className="ps__logo bitcoin"></span>
					</div>
				</div>
			</div>
		</section>
    );
}