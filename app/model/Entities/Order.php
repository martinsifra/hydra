<?php

namespace App\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nette;
use Pd;


/**
 * Objednavka, nebo rezervace
 *
 * @ORM\Entity
 * @ORM\Table("orders")
 *
 * @property int $id
 * @property string $orderNo
 * @property int $status
 * @property bool $isPaid
 * @property bool $isPartiallyShipped
 * @property Pd\Core\Web $web
 * @property string $firstName
 * @property string $lastName
 * @property string $phone
 * @property string $email
 * @property string $address
 * @property string $city
 * @property string $zip
 * @property string $country
 * @property string $company
 * @property string $ico
 * @property string $dic
 * @property string $note
 * @property string $dFirstName
 * @property string $dLastName
 * @property string $dPhone
 * @property string $dCompany
 * @property string $dAddress
 * @property string $dCity
 * @property string $dZip
 * @property string $dCountry
 * @property int $hcStatus
 * @property bool $isReservation
 * @property string $parcelNumber
 * @property string $extId
 * @property int $attempt
 * @property bool $reviewRequestSent
 *
 * @ property Pd\Orders\Item[] $items
 * @property Nette\Utils\DateTime $created
 * @property Pd\User\User $createdBy
 * @property Nette\Utils\DateTime $edited
 * @property Pd\User\User $editedBy
 * @property Pd\Product\Warehouse $warehouse
 * @property Pd\Orders\Items\PaymentItem $payment
 * @property Pd\Orders\Items\DeliveryItem $delivery
 * @property Pd\DateTime $requiredDeliveryDate
 * @property \DateTime $lastUpdated
 */
class Order extends Pd\Base\Entity
{
	const DEFAULT_WAREHOUSE = '✘'; // vychozi prodejna


	// bezne stavy
	const STATUS_UNFINISHED = 1;               // Nedokončená
	const STATUS_RECEIVED = 2;                 // Přijatá
	const STATUS_PROCESSING = 3;               // Zpracovává se
	const STATUS_READY_FOR_DISPATCH = 4;       // Připravená k expedici
	const STATUS_DISPATCHED = 5;               // Expedováno
	const STATUS_WAITING_FOR_DOC_CETELEM = 6;  // Čeká se na dokumenty pro Cetelem
	const STATUS_WAITING_FOR_PAYMENT = 7;      // Čeká se na platbu předem
	const STATUS_READY_FOR_PICKUP = 8;         // Připravena k vyzvednutí
	const STATUS_EXECUTED = 10;                // Dokončená

	// storno stavy
	const STATUS_CANCEL_ADMIN = 20;            // Storno

	const STATUS_FAILED = 91;                  // Chybná


	public static $allStatuses = array( // seznam vsech statusu
		self::STATUS_UNFINISHED,
		self::STATUS_RECEIVED,
		self::STATUS_PROCESSING,
		self::STATUS_READY_FOR_DISPATCH,
		self::STATUS_DISPATCHED,
		self::STATUS_READY_FOR_PICKUP,
		self::STATUS_EXECUTED,
		self::STATUS_WAITING_FOR_DOC_CETELEM,
		self::STATUS_WAITING_FOR_PAYMENT,
		self::STATUS_CANCEL_ADMIN,
		self::STATUS_FAILED,
	);

	public static $statusesCancel = array( // stornovana / zrusena
		self::STATUS_CANCEL_ADMIN,
	);

	public static $statusesCompleted = array( // dokoncena nebo zrusena (uz nemuze jit pryc)
		self::STATUS_CANCEL_ADMIN,
		self::STATUS_DISPATCHED,
	);


	/* modifikatory pro typ ceny, kterou chceme; viz getPrice() a obdobne */
	const PRICE_PRODUCT = 1;
	const PRICE_DELIVERY = 4;
	const PRICE_PAYMENT = 8;
	const PRICE_CREDIT = 16;
	const PRICE_VOUCHER = 32;

	const PRICE_COMBINED_ITEMS = 3; // všechny normální položky objednávky (kromě poplatků jako je doprava, platba, ...); PRODUCT
	const PRICE_COMBINED_CHARGES = 12; // poplatky; DELIVERY + PAYMENT
	const PRICE_COMBINED_NO_DISCOUNTS = 15; // celá cena, ale ještě beze slev; PRODUCT + DELIVERY + PAYMENT
	const PRICE_COMBINED_TO_PAY = 127; // celková cena, kteoru má zákazník zaplatit; PRODUCT + DELIVERY + PAYMENT - CREDIT - VOUCHER - DISCOUNT

	const EMAIL_RESUME = 1;

	/** typy telefoních čísel */
	const PHONE_DEFAULT = 'phone';
	const PHONE_DELIVERY = 'phone_delivery';
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @var int
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string", name="order_no")
	 * @var string
	 */
	protected $orderNo;

	/**
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	protected $status;

	/**
	 * @ORM\OneToMany(targetEntity=Pd\Orders\StatusChange::class, mappedBy="order", cascade={"persist"})
	 * @ORM\OrderBy({"changed" = "DESC", "id" = "DESC"})
	 * @var StatusChange[]|Collection
	 */
	protected $statusChanges;

	/**
	 * @ORM\Column(type="boolean")
	 * @var bool
	 */
	protected $isPaid;

	/**
	 * @ORM\Column(type="boolean", nullable=TRUE)
	 * @var bool
	 */
	protected $isPartiallyShipped = FALSE;

	/**
	 * @ ORM\ManyToOne(targetEntity="Pd\Core\Web", cascade={"persist"})
	 * @ ORM\JoinColumn(name="web_id", referencedColumnName="id")
	 * @ORM\Column(type="integer", name="web_id")
	 * @var Pd\Core\Web
	 */
	protected $web;

	/**
	 * @ ORM\ManyToOne(targetEntity="Pd\User\User", cascade={"persist"})
	 * @ ORM\JoinColumn(name="customer_id", referencedColumnName="id", nullable=TRUE)
	 * @ORM\Column(type="integer", name="customer_id")
	 * @var Pd\User\User
	 */
	protected $customer;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	protected $email;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	protected $firstName;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	protected $lastName;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	protected $phone;

	/**
	 * @ORM\Column(type="string", nullable=TRUE)
	 * @var string
	 */
	protected $company;

	/**
	 * @ORM\Column(type="string", nullable=TRUE)
	 * @var string
	 */
	protected $ico;

	/**
	 * @ORM\Column(type="string", nullable=TRUE)
	 * @var string
	 */
	protected $dic;

	/**
	 * @ORM\Column(type="string", name="street")
	 * @var string
	 */
	protected $address;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	protected $city;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	protected $zip;

	/**
	 * @ORM\Column(type="string", nullable=TRUE)
	 * @var string
	 */
	protected $country;

	/**
	 * @ORM\Column(type="string", nullable=TRUE)
	 * @var string
	 */
	protected $countryCode;

	/**
	 * @ORM\Column(type="string", nullable=TRUE)
	 * @var string
	 */
	protected $note;

	/**
	 * @ORM\Column(type="string", nullable=TRUE, name="note_admin")
	 * @var string
	 */
	protected $noteAdmin;

	/**
	 * @ORM\OneToMany(targetEntity=Pd\Orders\Item::class, mappedBy="order", cascade={"persist"}, indexBy="id")
	 * @ORM\OrderBy({"groupId" = "DESC", "typePosition" = "ASC", "position" = "ASC"})
	 * @var Item[]|Collection
	 */
	protected $items;

	/**
	 * @ORM\OneToMany(targetEntity="Pd\Orders\Items\DiscountItem", mappedBy="order", cascade={"persist"}, indexBy="id")
	 * @var Pd\Orders\Items\DiscountItem[]|Collection
	 */
	protected $discountItems;

	/**
	 * @ORM\OneToMany(targetEntity="Pd\Orders\Items\ProductItem", mappedBy="order", cascade={"persist"}, indexBy="id")
	 * @ORM\OrderBy({"groupId" = "DESC", "typePosition" = "ASC", "position" = "ASC"})
	 * @var Pd\Orders\Items\ProductItem[]|Collection
	 */
	protected $orderProductItems;

	/**
	 * @ORM\ManyToMany(targetEntity="Pd\Product\Item", fetch="EXTRA_LAZY", indexBy="id")
	 * @ORM\JoinTable(name="orders_items",
	 *    joinColumns={@ORM\JoinColumn(name="order_id", referencedColumnName="id")},
	 *    inverseJoinColumns={@ORM\JoinColumn(name="productItem_id", referencedColumnName="id")}
	 * )
	 * @var Pd\Product\Item[]
	 */
	protected $productItems;

	/**
	 * @ORM\ManyToMany(targetEntity="Pd\Voucher\VoucherCode", fetch="EXTRA_LAZY")
	 * @ORM\JoinTable(name="orders_items",
	 *    joinColumns={@ORM\JoinColumn(name="order_id", referencedColumnName="id")},
	 *    inverseJoinColumns={@ORM\JoinColumn(name="voucherCode_id", referencedColumnName="id")}
	 * )
	 * @var Pd\Voucher\VoucherCode[]
	 */
	protected $voucherCodes;

	/**
	 * @ORM\ManyToMany(targetEntity="Pd\Orders\DeliveryMethods\DeliveryMethod", fetch="EXTRA_LAZY")
	 * @ORM\JoinTable(name="orders_items",
	 *    joinColumns={@ORM\JoinColumn(name="order_id", referencedColumnName="id")},
	 *    inverseJoinColumns={@ORM\JoinColumn(name="delivery_id", referencedColumnName="id")}
	 * )
	 * @var Pd\Orders\DeliveryMethods\DeliveryMethod[]
	 */
	protected $deliveryMethods;

	/**
	 * @ORM\ManyToMany(targetEntity="Pd\Orders\PayMethods\PayMethod", fetch="EXTRA_LAZY")
	 * @ORM\JoinTable(name="orders_items",
	 *    joinColumns={@ORM\JoinColumn(name="order_id", referencedColumnName="id")},
	 *    inverseJoinColumns={@ORM\JoinColumn(name="payment_id", referencedColumnName="id")}
	 * )
	 * @var Pd\Orders\PayMethods\PayMethod[]
	 */
	protected $payMethods;

	/**
	 * @ORM\Column(type="datetime", nullable=TRUE)
	 * @var Pd\DateTime
	 */
	protected $created;

	/**
	 * @ORM\Column(type="integer", nullable=TRUE) TODO: relace
	 * @var Pd\User\User
	 */
	protected $createdBy;

	/**
	 * @ORM\Column(type="datetime", nullable=TRUE)
	 * @var Pd\DateTime
	 */
	protected $edited;

	/**
	 * @ORM\Column(type="integer", nullable=TRUE) TODO: relace
	 * @var Pd\User\User
	 */
	protected $editedBy;

	/**
	 * Sklad / prodejna, kde je osobni odber objednavky
	 *
	 * @ORM\ManyToOne(targetEntity="Pd\Product\Warehouse", cascade={"persist"})
	 * @ORM\JoinColumn(name="expeditionWarehouse_id", referencedColumnName="id", nullable=TRUE)
	 * @var Pd\Product\Warehouse
	 */
	protected $expeditionWarehouse;

	/**
	 * Cenová hladina ve které byla objednávka provedene (pokud není výchozí)
	 *
	 * @ORM\Column(type="integer", nullable=TRUE, name="priceLevel_id")
	 * @var Pd\Product\PriceLevel|int
	 */
	protected $priceLevel;

	/**
	 * @ORM\Column(type="string", nullable=TRUE)
	 * @var string
	 */
	protected $extId;

	/**
	 * @ORM\Column(type="string", nullable=TRUE)
	 * @var string
	 */
	protected $oldId;

	/**
	 * Sklad / prodejna, kde je osobni odber objednavky
	 *
	 * @ORM\ManyToOne(targetEntity="Pd\Product\Warehouse", cascade={"persist"})
	 * @ORM\JoinColumn(name="warehouse_id", referencedColumnName="id", nullable=TRUE)
	 * @var Pd\Product\Warehouse
	 */
	protected $warehouse;

	/**
	 * Kod doporucitele, na zaklade ktereho byla objednavka udelana
	 *
	 * @ORM\Column(type="string", nullable=TRUE, name="affiliate_code")
	 * @var string
	 */
	protected $affiliateCode;

	/**
	 * Doporucitel, na zaklade ktereho byla objednavka udelana
	 *
	 * @ ORM\ManyToOne(targetEntity="Pd\User\User", cascade={"persist"})
	 * @ ORM\JoinColumn(name="affiliateUser_id", referencedColumnName="id")
	 * @ORM\Column(type="integer", nullable=TRUE, name="affiliate_user_id")
	 * @var Pd\User\User
	 */
	protected $affiliateUser;

	/**
	 * @ORM\Column(type="integer", nullable=TRUE, name="assignedUser_id")
	 * @var Pd\User\User
	 */
	protected $assignedUser;

	/**
	 * @ORM\Column(type="boolean", nullable=TRUE, name="hasBlockedMailNotifications")
	 * @var boolean
	 */
	protected $hasBlockedMailNotifications = FALSE;

	/**
	 * @ORM\Column(type="boolean", nullable=TRUE, name="hasBlockedSmsNotifications")
	 * @var boolean
	 */
	protected $hasBlockedSmsNotifications = FALSE;

	/**
	 * @ORM\Column(type="boolean", options={"default" = FALSE})
	 * @var boolean
	 */
	protected $hasExportError = FALSE;

	/**
	 * @ORM\Column(type="integer")
	 * @var int Pocitadlo pokusu o zaplaceni; aby se do platebni brany posilalo unikatni cislo
	 */
	protected $attempt = 0;

	/**
	 * @ORM\Column(type="boolean")
	 * @var bool
	 */
	protected $reviewRequestSent = FALSE;

	/**
	 * @ORM\Column(type="datetime", nullable=TRUE)
	 * @var Pd\DateTime
	 */
	protected $requiredDeliveryDate;

	/**
	 * @ORM\Column(type="string", nullable=TRUE)
	 * @var string
	 */
	protected $gopayPaymentSessionId;

	/**
	 * @ORM\Column(type="string", nullable=TRUE)
	 * @var string
	 */
	protected $type;

	/**
	 * @ORM\Column(type="datetime", nullable=TRUE)
	 * @var Pd\DateTime
	 */
	protected $typeExpire;

	/**
	 * @ORM\Column(type="string", nullable=TRUE)
	 * @var string
	 */
	protected $importHash;

	/**
	 * @ORM\Column(type="datetime", nullable=TRUE)
	 * @var Pd\DateTime
	 */
	protected $statusWaitingForPayment;

	/**
	 * @ORM\Column(type="boolean")
	 * @var bool
	 */
	protected $isReservation = FALSE;

	/**
	 * @ORM\Column(type="string", nullable=TRUE)
	 * @var string
	 */
	protected $webpayTypeId;

	/**
	 * @ORM\Column(type="string", nullable=TRUE)
	 * @var string
	 */
	protected $webpayTransactionId;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	protected $webpayResult;


	/**
	 * @ORM\Column(type="datetime", nullable=TRUE)
	 * @var Pd\DateTime
	 */
	protected $exportedDate;


	/**
	 * @ORM\Column(type="boolean", nullable=TRUE)
	 * @var bool
	 */
	protected $isExported = FALSE;

	/**
	 * @ORM\OneToMany(targetEntity="Pd\OrderModule\OrderNote", mappedBy="order", cascade={"persist"})
	 * @ORM\OrderBy({"created" = "DESC"})
	 * @var Pd\OrderModule\OrderNote[]|Collection
	 */
	protected $notes;


	/**
	 * @ORM\Column(type="boolean", nullable=TRUE)
	 * @var bool
	 */
	protected $hasNotAnsweredNote = FALSE;


	/**
	 * Bitwise
	 * @ORM\Column(type="integer")
	 * @var integer
	 */
	protected $emailSent;


	/**
	 * @ORM\Column(type="string", nullable=TRUE)
	 * @var string
	 */
	protected $instalmentStatus;


	/**
	 * @ORM\Column(type="array", nullable=TRUE)
	 * @var array
	 */
	protected $instalmentData;


	/**
	 * @ORM\Column(type="string", nullable=TRUE)
	 * @var string
	 */
	protected $instalmentExtId;

	/**
	 * @ORM\Column(type="float")
	 * @var float
	 */
	protected $priceIncVat = 0;

	/**
	 * @ORM\Column(type="float")
	 * @var float
	 */
	protected $priceExcVat = 0;

	/**
	 * @ORM\Column(type="string", nullable=TRUE)
	 * @var string
	 */
	protected $ip;

	/**
	 * @ORM\Column(type="boolean")
	 * @var bool
	 */
	protected $isStoreOrder = FALSE;

	/**
	 * @ORM\ManyToOne(targetEntity="Pd\Product\Warehouse", cascade={"persist"})
	 * @ORM\JoinColumn(name="store_id", referencedColumnName="id", nullable=TRUE)
	 * @var Pd\Product\Warehouse
	 */
	protected $store;

	/**
	 * @ORM\Column(type="boolean")
	 * @var bool
	 */
	protected $paymentWithVat = TRUE;

	/**
	 * @ORM\Column(type="string", nullable=TRUE)
	 * @var string
	 */
	protected $cardLevel;

	/**
	 * @ORM\Column(type="boolean", nullable=TRUE, name="hasBlockedInternalNotifications")
	 * @var boolean
	 */
	protected $hasBlockedInternalNotifications = FALSE;


	/**
	 * @ORM\Column(type="datetime", nullable=TRUE)
	 * @var \DateTime
	 */
	protected $lastUpdated;

	/**
	 * @ORM\Column(type="array", nullable=TRUE)
	 * @var array
	 */
	protected $data;


	public function __construct(array $data = null)
	{
		parent::__construct($data);
		$this->items = new ArrayCollection;
		$this->statusChanges = new ArrayCollection;
		$this->notes = new ArrayCollection;
	}



	/**
	 * Zmena stavu; zajisti, aby byl vzdy int (jinak se muzou rozbit navazane handlery)
	 * @param int $status
	 */
	public function setStatus($status)
	{
		$this->status = (int) $status;
	}



	/********************** polozky objednavky **********************/

	/**
	 * Položky objednávky (produkty, kurzy, doprava, platba, ...)
	 *
	 * @return \Pd\Orders\Item[]
	 */
	public function getItems()
	{
		return $this->items->toArray();
	}


	/**
	 * Položky jen daného typu
	 *
	 * @param string|string[] $type Typ itemu, viz Pd\Orders\Items\Item::TYPE_*
	 * @return \Pd\Orders\Item[]
	 */
	public function getItemsByType($type = Pd\Orders\Item::TYPE_PRODUCT)
	{
		$type = (array) $type;
		$ret = array();
		foreach ($this->getItems() as $k => $item) {
			if (in_array($item->getItemType(), $type)) {
				$ret[$k] = $item;
			}
		}

		return $ret;
	}


	public function clearItems()
	{
		$this->priceIncVat = $this->priceExcVat = 0;
		$this->items->clear();
	}


	/**
	 * Prida polozku k objednavce
	 *
	 * @param \Pd\Orders\Item $item
	 */
	public function addItem(Pd\Orders\Item $item)
	{
		$item->order = $this;
		$this->items->add($item);
		$this->priceIncVat += $item->priceIncVat;
		$this->priceExcVat += $item->priceExcVat;

		return $item;
	}


	/**
	 * Celkový počet produktů (a kurzů) v objednávce
	 *
	 * @return int|float
	 */
	public function getProductTotalQuantity()
	{
		return array_sum(array_map(function (Pd\Orders\Item $item) {
			return $item->quantity * 1;
		}, $this->getItemsByType(array(Pd\Orders\Item::TYPE_PRODUCT, Pd\Orders\Item::TYPE_COURSE))));
	}


	/**
	 * @param Pd\Product\Item $item
	 * @param float $quantity
	 * @param string $priceLevel
	 * @param Pd\Product\ParameterValue[] $parameterValues
	 * @return Pd\Orders\Items\ProductItem
	 */
	public function addProductItem(Pd\Product\Item $item, $quantity = 1.0, $priceLevel = NULL, $parameterValues = NULL)
	{
		return $this->addItem(new Pd\Orders\Items\ProductItem($item, $quantity, $priceLevel, $parameterValues));
	}


	public function addDeliveryMethodExtraServiceItem(Pd\Product\Item $item, $priceLevel = NULL)
	{
		return $this->addItem(new Pd\Orders\Items\ExtraServiceItem($this->getDelivery(), $item, 1, $priceLevel));
	}


	/********************** dalsi atributy **********************/

	public function getWeb()
	{
		return $this->webId ? Nette\Environment::getService('webService')->fetch($this->webId) : NULL;
	}


	/**
	 * @return Pd\User\User
	 */
	public function getCreatedBy()
	{
		return $this->fetchManyHasOne($this->getService('userService'), 'createdBy');
	}


	/**
	 * @return Pd\User\User
	 */
	public function getEditedBy()
	{
		return $this->fetchManyHasOne($this->getService('userService'), 'editedBy');
	}


	/**
	 * @return Pd\User\User
	 */
	public function getAffiliateUser()
	{
		return $this->fetchManyHasOne($this->getService('userService'), 'affiliateUser');
	}


	/**
	 * @return Pd\Orders\Items\DeliveryItem
	 */
	public function getDelivery()
	{
		return current($this->getItemsByType(Pd\Orders\Item::TYPE_DELIVERY));
	}


	/**
	 * @return Pd\Orders\Items\PaymentItem
	 */
	public function getPayment()
	{
		return current($this->getItemsByType(Pd\Orders\Item::TYPE_PAYMETHOD));
	}

	/**
	 * Sklad, na kterem je osobni odber
	 *
	 * @return Pd\Product\Warehouse
	 */
	public function getWarehouse()
	{
		return $this->fetchManyHasOne($this->getService('warehouseService'), 'warehouse');
	}


	/**
	 * Sklad, na kterem je osobni odber
	 *
	 * @return Pd\Product\Warehouse
	 */
	public function getStore()
	{
		return $this->fetchManyHasOne($this->getService('warehouseService'), 'store');
	}



	/********************** cena **********************/


	/**
	 * Celková cena bez DPH
	 * @param int $type Jedna hodnota nebo bitova kombinace z kontant PRICE_*
	 *
	 * @return Pd\Price\IPrice
	 */
	public function getPrice($type)
	{
		$prices = [];

		foreach ($this->getItems() as $item) {
			switch(TRUE) {
				case ($item instanceof Pd\Orders\Items\ReceiptItem):
					continue 2;

				case ($item instanceof Pd\Orders\Items\ProductItem):
				case ($item instanceof Pd\Orders\Items\CourseItem):
				case ($item instanceof Pd\Orders\Items\DiscountItem):
					if (($type & self::PRICE_PRODUCT) === 0) continue 2;
					break;

				case ($item instanceof Pd\Orders\Items\DeliveryItem):
					if (($type & self::PRICE_DELIVERY) === 0) continue 2;
					break;

				case ($item instanceof Pd\Orders\Items\PaymentItem):
					if (($type & self::PRICE_PAYMENT) === 0) continue 2;
					break;

				case ($item instanceof Pd\Orders\Items\VoucherItem):
					if (($type & self::PRICE_VOUCHER) === 0) continue 2;
					break;

				case ($item instanceof Pd\Orders\Items\CreditItem):
					if (($type & self::PRICE_CREDIT) === 0) continue 2;
					break;
			}


			$prices[] = $item->totalPrice;
		}

		return Pd\Price\PriceCalc::sum($prices);
	}


	public function getItemsPrice()
	{
		return $this->getPrice(self::PRICE_COMBINED_ITEMS);
	}


	public function getChargesPrice()
	{
		return $this->getPrice(self::PRICE_COMBINED_CHARGES);
	}


	public function getNoDiscountsPrice()
	{
		return $this->getPrice(self::PRICE_COMBINED_NO_DISCOUNTS);
	}


	public function getToPayPrice()
	{
		$price = $this->getPrice(self::PRICE_COMBINED_TO_PAY);
		$amountIncVat = $price->getAmountIncVat();
		$amountExcVat = $price->getAmountExcVat();
		if ($amountIncVat < 0 || $amountExcVat < 0) {
			return Pd\Price\Price::getZeroPrice();
		}

		return $price;
	}


	/**
	 * Přidá text k poznámce pro administrátora
	 *
	 * @param string $note
	 * @return void
	 */
	public function addNoteAdmin($note)
	{
		$this->noteAdmin = trim("$this->noteAdmin\n\n$note");
	}


	/**
	 * Přidá text k poznámce uživatele
	 *
	 * @param string $note
	 * @return void
	 */
	public function addNote($note)
	{
		$this->note = trim("$this->note\n\n$note");
	}


	/**
	 * @return Pd\Product\PriceLevel
	 */
	public function getPriceLevel()
	{
		return $this->fetchManyHasOne($this->getService('priceLevelService'), 'priceLevel');
	}


	public function getLastStatusChange($status)
	{
		return $this->statusChanges->filter(function(StatusChange $statusChange) use ($status) {
			return $statusChange->status === $status;
		})->first();
	}


	/**
	 * @return number
	 */
	public function getCardBonusSum()
	{
		$sum = 0.0;
		foreach ($this->items as $item) {
			$sum += $item->cardBonus * $item->quantity;
		}

		return $sum;
	}


	public function setPhone($phone)
	{
		if ($phone) {
			$this->phone = $this->getService('orderService')->parsePhone($phone, $this, self::PHONE_DEFAULT);
		}
	}


	public function getPriceIncVat()
	{
		return new Pd\Price\Price($this->priceIncVat, $this->priceIncVat);
	}


	public function getPriceExcVat()
	{
		return new Pd\Price\Price($this->priceExcVat, $this->priceExcVat);
	}
}
