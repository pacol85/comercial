<?php

namespace Defr;

/**
 * Class LoanRequest.
 *
 * @author Modified from work of Dennis Fridrich <fridrich.dennis@gmail.com>
 */
class LoanRequest extends MortageRequest
{
	/**
	 * @var float
	 */
	private $salePrice;

	/**
	 * @var float
	 */
	private $interestPercent;

	/**
	 * @var int
	 */
	private $monthTerm;

	/**
	 * Mortage constructor.
	 *
	 * @param int $salePrice
	 * @param int $interestPercent
	 * @param int $monthTerm
	 */
	public function __construct(
			$salePrice = 0,
			$interestPercent = 0,
			$monthTerm = 0
	) {
		$this->salePrice = (float) $salePrice;
		$this->interestPercent = (float) $interestPercent;
		$this->monthTerm = (int) $monthTerm;		
	}

	/**
	 * @return LoanResult
	 */
	public function calculate()
	{
		if ($this->salePrice <= 0) {
			throw new \InvalidArgumentException('Monto de prestamo no puede ser menor a 0.');
		}
		if ($this->interestPercent <= 0) {
			throw new \InvalidArgumentException('Interes no puede ser menor a 0.');
		}
		if ($this->monthTerm <= 0) {
			throw new \InvalidArgumentException('Los meses no pueden ser 0.');
		}

		$annualInterestRate = $this->interestPercent / 100;
		$monthlyInterestRate = $annualInterestRate / 12;
		//$dailyInterestRate = $monthlyInterestRate / 30;

		//$monthlyPayment = $this->salePrice / $this->getInteresMensual($this->monthTerm, $annualInterestRate);
		$mp = $this->getCalcCompuesto($this->salePrice, $this->monthTerm, $annualInterestRate);

		$result = new MortageResult(
				$this,
				$annualInterestRate,
				$monthlyInterestRate,
				$mp//$monthlyPayment
		);

		return $result;
	}

	/**
	 * @param float $salePrice
	 */
	public function setSalePrice($salePrice)
	{
		$this->salePrice = (float) $salePrice;
	}

	/**
	 * @param float $mortgageInterestPercent
	 */
	public function setInterestPercent($interestPercent)
	{
		$this->interestPercent = (float) $interestPercent;
	}

	/**
	 * @param bool $showProgress
	 */
	public function setShowProgress($showProgress)
	{
		$this->showProgress = (bool) $showProgress;
	}

	/**
	 * @param $prestamo
	 * @param $month_term
	 * @param $yearly_interest_rate
	 *
	 * @return float|int
	 */
	private function getCalcCompuesto($prestamo, $month_term, $yearly_interest_rate)
	{
		$r = $yearly_interest_rate / 12;
		$dividendo = $r * $prestamo;
		$n = $month_term;
		$divisor = 1 - pow((1 + $r), -$n);
		$factor = $dividendo / $divisor;
		return $factor;
	}
	
	/**
	 * @param $prestamo
	 * @param $month_term
	 * @param $yearly_interest_rate
	 *
	 * @return float|int
	 */
	private function getCalcSimple($prestamo, $month_term, $yearly_interest_rate)
	{
		$tasaMensual = $yearly_interest_rate/12;
		$totalIMensual = $prestamo * $tasaMensual;
		$cuotaNoI = $prestamo / $month_term;
		$factor = $cuotaNoI + $totalIMensual;
		return $factor;
	}

	/**
	 * @return int
	 */
	public function getMonthTerm()
	{
		return $this->monthTerm;
	}

	/**
	 * @return float
	 */
	public function getSalePrice()
	{
		return $this->salePrice;
	}

	/**
	 * @return float
	 */
	public function getMortgageInterestPercent()
	{
		return $this->mortgageInterestPercent;
	}	
}
