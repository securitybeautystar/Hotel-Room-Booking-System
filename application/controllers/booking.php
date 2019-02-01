<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

/**
 * Class : Booking (BookingController)
 * Booking Class to control all booking related operations.
 * @author : Kishor Mali
 * @version : 1.1
 * @since : 29 Mar 2017
 */
class Booking extends BaseController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Booking_model', "booking");
        $this->load->model('rooms_model');
        $this->isLoggedIn();   
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        redirect("book");
    }

    /**
     * This function is used to load the rooms list
     */
    function bookings()
    {
        $searchText = $this->input->post('searchText');
        $searchFloorId = $this->input->post('floorId');
        $searchRoomSizeId = $this->input->post('sizeId');
        $searchRoomId = $this->input->post('roomId');
        $data['searchText'] = $searchText;
        $data['searchRoomId'] = $searchRoomId;
        $data['searchFloorId'] = $searchFloorId;
        $data['searchRoomSizeId'] = $searchRoomSizeId;
        $data['rooms'] = $this->rooms_model->getRooms();
        $data['roomSizes'] = $this->rooms_model->getRoomSizes();
        $data['floors'] = $this->rooms_model->getFloors();

        $this->load->library('pagination');
        
        $count = $this->booking->bookingCount($searchText, $searchRoomId, $searchFloorId, $searchRoomSizeId);

        $returns = $this->paginationCompress ( "book/", $count, 10);
        
        $data['bookingRecords'] = $this->booking->bookingListing($searchText, $searchRoomId, $searchFloorId, $searchRoomSizeId, $returns["page"], $returns["segment"]);
        
        $this->global['pageTitle'] = 'DigiLodge : Bookings';
        
        $this->loadViews("bookings/bookingIndex", $this->global, $data, NULL);
    }

    /**
     * This function is used to load the add new form
     */
    function addNewBooking()
    {
        $this->global['pageTitle'] = 'DigiLodge : Book the room';

        $data['floors'] = $this->rooms_model->getFloors();
        $data['roomSizes'] = $this->rooms_model->getRoomSizes();

        $this->loadViews("bookings/addNewBooking", $this->global, $data, NULL);
    }

    /**
     * Get room list by floor and size
     * @param {number} $floorId : This is floor id
     * @param {number} $sizeId : This is size id
     */
    function getRoomsByFT()
    {
        $sizeId = $this->input->post('sizeId') == '' ? 0 : $this->input->post('sizeId') ;
        $floorId = $this->input->post('floorId') == '' ? 0 : $this->input->post('floorId');

        $result = $this->rooms_model->getRoomsByFT($floorId, $sizeId);

        echo(json_encode(array('rooms'=>$result)));
    }
}