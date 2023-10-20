import React, { useEffect, useState } from "react";
import {
  Accordion,
  AccordionBody,
  AccordionHeader,
  AccordionItem,
  Button,
  Card,
  CardBody,
  Col,
  Container,
  Label,
  Modal,
  ModalBody,
  ModalFooter,
  ModalHeader,
  Row,
} from "reactstrap";
import { useDispatch, useSelector } from "react-redux";
import Toast from "../../constants/toast";
import { Field, Form, Formik, ErrorMessage } from "formik";
import { disabledPastDateTimeLocale } from "../../constants/disabledPreviousDates";
import {
  createCalendarEvent,
  getCalendarEvents,
  updateCalendarEvent,
  deleteCalendarEvent,
  createNewCalendar,
  getCalendars,
  deleteCalendar,
  shareCalendar,
  shareCalendarWith,
  deleteShareCalendarWith,
} from "../../../redux/actions/googleCalendarActions";
import { CalendarLoader, DataLoader, Loader } from "../../constants/loaders";
import asterik from "../../../assets/images/asterisk.png";
import FullCalendar from "@fullcalendar/react"; // must go before plugins
import dayGridPlugin from "@fullcalendar/daygrid"; // a plugin!
import { EventOverviewModal } from "./Modals/EventOverviewModal";

import interactionPlugin from "@fullcalendar/interaction";
import moment from "moment";
import axios from "axios";
import { baseURL } from "../../Shared/baseURL";
import Calendar from "react-calendar";
import "react-calendar/dist/Calendar.css";
import { FcGoogle } from "react-icons/fc";
import { MdOutlineLogout } from "react-icons/md";
import { GrUserSettings } from "react-icons/gr";
import Select from "react-select";
import { getUserProfile } from "../../../redux/actions/usersAction";
import { useTranslation } from "react-i18next";
import {
  CalendarValidationSchema,
  EventValidationSchema,
} from "./validations/formValidation";
import { validateHtmlTags } from "./validations/validateFuncations";
import {
  SubmitFormValidationErrors,
  FormValidationErrors,
} from "../../constants/errors";

const GoogleCalendar = ({ fixNavbar, modalVisible, toggleCalendar }) => {
  const dispatch = useDispatch();

  const { t } = useTranslation();

  const [selectedEvent, setSelectedEvent] = useState({});
  const [value, onChange] = useState(new Date());
  const [open, setOpen] = useState("1");
  const toggle = (id) => {
    if (open === id) {
      setOpen();
    } else {
      setOpen(id);
    }
  };

  const [calendarIDS, setCalendarIDS] = useState([]);
  const [calendarValue, setCalendarValue] = useState([]);
  const [isLoading, setIsLoading] = useState(false);

  // Share calendar states
  const [shareCalendarModal, setShareCalendarModal] = useState(false);
  const [shareCalendarUserOptions, setShareCalendarUserOptions] = useState([]);
  const [shareCalendarSettingsModal, setShareCalendarSettingsModal] =
    useState(false);
  const [shareCalendarUserIds, setShareCalendarUserIds] = useState([]);
  const [shareCalendarRoleValue, setShareCalendarRoleValue] = useState("");
  const [shareCalendarID, setShareCalendarID] = useState(null);

  // ERRORS STATE
  const [errors, setErrors] = useState({});
  const [formErrors, setFormErrors] = useState({});

  const toggleShareCalendarModal = () => {
    setShareCalendarModal(!shareCalendarModal);
  };

  const toggleShareCalendarSettingsModal = () => {
    setShareCalendarSettingsModal(!shareCalendarSettingsModal);
  };

  const userProfileSelector = useSelector(
    (state) => state.users.user_profile.data
  );
  const userSelector = useSelector((state) => state.users.data.data);
  const calendarSelector = useSelector((state) => state.calendar);
  const settingsSelector = useSelector((state) => state.settings);

  useEffect(() => {
    if (userSelector && userSelector !== undefined) {
      let userOptions = [];
      userSelector.Users.data.forEach((item) => {
        userOptions.push({ value: item.userpr_id, label: item.name });
      });
      setShareCalendarUserOptions(userOptions);
    }
  }, [userSelector]);

  const initialValues = {
    id: "",
    name: "",
    description: "",
    is_all_day: true,
    start_date_time: "",
    end_date_time: "",
    calendar_id: [],
    transparency: "opaque",
  };

  const [formValues, setFormValues] = useState(initialValues);
  const [modal, setModal] = useState(false);
  const [newCalendarModal, setNewCalendarModal] = useState(false);
  const [eventModal, setEventModal] = useState(false);
  const [updateEvent, setUpdateEvent] = useState(false);
  const [selectCalendarIDS, setSelectCalendarIDS] = useState([]);
  const [calendarValidationCheck, setCalendarValidationCheck] = useState(false);

  const toggleModal = () => {
    setModal(!modal);

    if (updateEvent === true) {
      setUpdateEvent(false);
    }
  };
  const toggleNewCalendarModal = () => {
    setNewCalendarModal(!newCalendarModal);
  };

  const fetchUserProfile = async () => {
    try {
      const response = await dispatch(getUserProfile());
    } catch (error) {
      throw error;
    }
  };

  const fetchCalendarEvents = async () => {
    try {
      dispatch(getCalendars());
    } catch (error) {
      throw error;
    }
  };

  useEffect(() => {
    fetchCalendarEvents();
  }, []);

  const [calendarOptions, setCalendarOptions] = useState([]);

  useEffect(() => {
    if (calendarSelector.calendars.data) {
      let options = [];
      calendarSelector.calendars.data.personal.forEach((item) => {
        options.push({ value: item.id, label: item.title });
      });
      setCalendarOptions(options);

      let calendar_ids = [];
      calendarSelector.calendars.data.personal.map((item) => {
        if (item.is_checked === 1) {
          calendar_ids.push(item.id);
        }
      });
      setSelectCalendarIDS(calendar_ids);
      dispatch(getCalendarEvents(calendar_ids));
    }
  }, [calendarSelector.calendars.data]);

  const handleAddNewEventSubmit = (values) => {
    values["start_date_time"] = moment(values.start_date_time).format(
      "YYYY-MM-DD HH:mm:ss"
    );
    values["end_date_time"] = moment(values.end_date_time).format(
      "YYYY-MM-DD HH:mm:ss"
    );

    if (updateEvent) {
      values["calendar_id"] = calendarValue[0].value;
    } else {
      if (calendarIDS.length > 0) {
        values["calendar_id"] = calendarIDS;
        setCalendarValidationCheck(false);
      } else {
        setCalendarValidationCheck(true);
      }
    }

    // validate
    let error = {};
    error = validateHtmlTags(values, setFormErrors);

    if (Object.keys(error).length === 0 && !calendarValidationCheck) {
      if (updateEvent) {
        dispatch(updateCalendarEvent(values, setModal));
      } else {
        dispatch(createCalendarEvent(values, setModal));
      }
      fetchCalendarEvents();
    }
  };

  const handleDateClick = (arg) => {
    let start_date_time = moment(
      arg.dateStr + " " + moment().format("HH:mm:ss")
    ).set({ seconds: 0 });
    let nearest_half_hour = 30 - (start_date_time.minute() % 30);
    start_date_time = moment(start_date_time)
      .add(nearest_half_hour, "minutes")
      .format("YYYY-MM-DD HH:mm:ss");

    setFormValues({
      start_date_time: start_date_time,
      end_date_time: moment(start_date_time)
        .add(30, "minutes")
        .format("YYYY-MM-DD HH:mm:ss"),
    });
    setModal(true);
    setUpdateEvent(false);
  };

  const handleEditButton = function () {
    let event_id = selectedEvent["event"]["_def"]["extendedProps"]["eventid"];
    let title = selectedEvent["event"]["_def"]["title"];
    let description =
      selectedEvent["event"]["_def"]["extendedProps"]["description"];
    let all_day = selectedEvent["event"]["_def"]["allDay"];
    let start_day = selectedEvent["event"]["_def"]["extendedProps"]["startDay"];
    let end_day = selectedEvent["event"]["_def"]["extendedProps"]["endDay"];

    // FORMATTED DATES
    let startDate = moment(start_day).format("YYYY-MM-DD HH:mm:ss");
    let endDate = moment(end_day).format("YYYY-MM-DD HH:mm:ss");

    let calendarValues =
      selectedEvent["event"]["_def"]["extendedProps"]["calendar"];
    calendarValue.push({
      value: calendarValues.id,
      label: calendarValues.title,
    });

    let transparencyValue =
      selectedEvent["event"]["_def"]["extendedProps"]["transparency"];

    setFormValues({
      id: event_id,
      name: title,
      description: description,
      is_all_day: all_day,
      start_date_time: startDate,
      end_date_time: endDate,
      transparency: transparencyValue,
    });

    setEventModal(false);
    setUpdateEvent(true);
    setModal(true);
  };

  function handleDeleteEvent() {
    let event_id = selectedEvent["event"]["_def"]["extendedProps"]["eventid"];

    if (
      window.confirm("Kas oled kindel, et soovid kustutada selle sündmuse?") ===
      true
    ) {
      dispatch(deleteCalendarEvent(event_id));
      fetchCalendarEvents();
      setEventModal(false);
      setModal(false);
      setUpdateEvent(false);
    }
  }

  const handleEventClick = (event) => {
    setSelectedEvent(event);
    setModal(false);
    setEventModal(true);
    setUpdateEvent(false);
  };

  const handleCalendarIds = (e) => {
    if (updateEvent) {
      setCalendarIDS(e.value);
    } else {
      if (e.length > 0) {
        setCalendarValidationCheck(false);
        let selectedCalendarIds = [];
        e.map((item) => {
          selectedCalendarIds.push(item.value);
        });
        setCalendarIDS(selectedCalendarIds);
      } else {
        setCalendarValidationCheck(true);
      }
    }
  };

  const renderEventModal = () => {
    return (
      <div>
        <Modal isOpen={modal} toggle={toggleModal}>
          <ModalHeader toggle={toggleModal} className="mb-2">
            {updateEvent ? (
              <>{t("calendar.modals.updateEventHeading")}</>
            ) : (
              <>{t("calendar.modals.createEventModal.heading")}</>
            )}
          </ModalHeader>

          {/*FORM INPUT ERRORS*/}
          {formErrors && Object.keys(formErrors).length !== 0 ? (
            <SubmitFormValidationErrors formErrors={formErrors} />
          ) : null}

          <Formik
            initialValues={formValues}
            enableReinitialize
            validationSchema={EventValidationSchema}
            onSubmit={(values) => handleAddNewEventSubmit(values)}
          >
            <Form>
              <fieldset disabled={calendarSelector.isLoading}>
                <ModalBody>
                  <Row>
                    <Col sm={12} md={12}>
                      <Label>
                        {t("calendar.modals.createEventModal.name")}{" "}
                        <img
                          src={asterik}
                          height={10}
                          width={10}
                          className="mt-n2"
                        />
                      </Label>
                      <Field
                        type={"text"}
                        placeholder={"Nimetus"}
                        className={"form-control"}
                        name={"name"}
                        required
                      />
                      <ErrorMessage name={`name`}>
                        {(msg) => <span style={{ color: "red" }}>{msg}</span>}
                      </ErrorMessage>
                      <Field type={"hidden"} name={"id"} />
                    </Col>
                    <Col sm={12} md={12}>
                      <Label>
                        {t("calendar.modals.createEventModal.description")}{" "}
                        <img
                          src={asterik}
                          height={10}
                          width={10}
                          className="mt-n2"
                        />
                      </Label>
                      <Field
                        type={"textarea"}
                        placeholder={"Kirjeldus"}
                        className={"form-control"}
                        name={"description"}
                        required
                      />
                      <ErrorMessage name={`description`}>
                        {(msg) => <span style={{ color: "red" }}>{msg}</span>}
                      </ErrorMessage>
                    </Col>
                    <Col sm={12} md={12}>
                      <Label>
                        {t("calendar.modals.createEventModal.allDay")}{" "}
                        <Field type={"checkbox"} name={"is_all_day"} />
                      </Label>
                    </Col>
                    <Col sm={12} md={12}>
                      <Label>
                        {t(
                          "calendar.modals.createEventModal.eventStartDateAndTime"
                        )}{" "}
                        <img
                          src={asterik}
                          height={10}
                          width={10}
                          className="mt-n2"
                        />
                      </Label>
                      <Field
                        type={"datetime-local"}
                        className={"form-control"}
                        name={"start_date_time"}
                        step="any"
                        min={disabledPastDateTimeLocale()}
                        required
                      />
                      <ErrorMessage name={`start_date_time`}>
                        {(msg) => <span style={{ color: "red" }}>{msg}</span>}
                      </ErrorMessage>
                    </Col>
                    <Col sm={12} md={12}>
                      <Label>
                        {t(
                          "calendar.modals.createEventModal.eventEndDateAndTime"
                        )}{" "}
                        <img
                          src={asterik}
                          height={10}
                          width={10}
                          className="mt-n2"
                        />
                      </Label>
                      <Field
                        type={"datetime-local"}
                        className={"form-control"}
                        name={"end_date_time"}
                        step="any"
                        min={disabledPastDateTimeLocale()}
                        required
                      />
                      <ErrorMessage name={`end_date_time`}>
                        {(msg) => <span style={{ color: "red" }}>{msg}</span>}
                      </ErrorMessage>
                    </Col>
                    <Col sm={12} md={12}>
                      <Label>
                        {t("calendar.modals.createEventModal.calendarSelect")}{" "}
                        <img
                          src={asterik}
                          height={10}
                          width={10}
                          className="mt-n2"
                        />
                      </Label>
                      <Select
                        options={calendarOptions}
                        isMulti={updateEvent ? false : true}
                        closeMenuOnSelect={updateEvent ? true : false}
                        name={"calendar_id"}
                        onChange={(e) => handleCalendarIds(e)}
                        defaultValue={updateEvent ? calendarValue : []}
                      />
                      {calendarValidationCheck && (
                        <span style={{ color: "red" }}>Required</span>
                      )}
                    </Col>
                    <Col sm={12} md={12}>
                      <Label>
                        {t(
                          "calendar.modals.createEventModal.availability.heading"
                        )}{" "}
                        <img
                          src={asterik}
                          height={10}
                          width={10}
                          className="mt-n2"
                        />
                      </Label>
                      <div className={"d-flex"}>
                        <Label className={"mr-3"}>
                          <Field
                            type={"radio"}
                            name={"transparency"}
                            value={"transparent"}
                            required
                          />{" "}
                          {t(
                            "calendar.modals.createEventModal.availability.free"
                          )}{" "}
                        </Label>
                        <Label>
                          <Field
                            type={"radio"}
                            name={"transparency"}
                            value={"opaque"}
                            required
                            // checked={true}
                          />{" "}
                          {t(
                            "calendar.modals.createEventModal.availability.busy"
                          )}{" "}
                        </Label>
                      </div>
                      <ErrorMessage name={`transparency`}>
                        {(msg) => <span style={{ color: "red" }}>{msg}</span>}
                      </ErrorMessage>
                    </Col>
                  </Row>
                </ModalBody>
                <ModalFooter>
                  <Button color="primary" disabled={calendarSelector.isLoading}>
                    {calendarSelector.isLoading ? (
                      <Loader />
                    ) : updateEvent ? (
                      <>{t("calendar.buttons.updateEvent")}</>
                    ) : (
                      <>{t("calendar.buttons.createNewEvent")}</>
                    )}
                  </Button>{" "}
                  <Button color="secondary" onClick={toggleModal}>
                    {t("buttons.cancel")}
                  </Button>
                </ModalFooter>
              </fieldset>
            </Form>
          </Formik>
        </Modal>
      </div>
    );
  };

  const CreateNewCalendar = (value) => {
    // validate
    let error = {};
    error = validateHtmlTags(value, setFormErrors);

    if (Object.keys(error).length === 0) {
      dispatch(
        createNewCalendar(
          value,
          setNewCalendarModal,
          fetchCalendarEvents,
          setErrors
        )
      );
    }
  };

  const handleDeleteCalendar = (e, calendarID) => {
    const confirm = window.confirm("Are You Sure?");
    if (confirm === true) {
      dispatch(deleteCalendar(calendarID));
    }
  };

  const renderCreateNewCalendarModal = () => {
    return (
      <Modal isOpen={newCalendarModal} toggle={toggleNewCalendarModal}>
        <ModalHeader toggle={toggleNewCalendarModal} className={"mb-2"}>
          {t("calendar.modals.createCalendarModal.heading")}
        </ModalHeader>

        {/*ERRORS*/}
        {errors && Object.keys(errors).length !== 0 ? (
          <FormValidationErrors errors={errors} />
        ) : null}

        {/*FORM INPUT ERRORS*/}
        {formErrors && Object.keys(formErrors).length !== 0 ? (
          <SubmitFormValidationErrors formErrors={formErrors} />
        ) : null}

        <Formik
          initialValues={{ title: "" }}
          enableReinitialize
          validationSchema={CalendarValidationSchema}
          onSubmit={(values) => CreateNewCalendar(values)}
        >
          <Form>
            <fieldset disabled={calendarSelector.isLoading}>
              <ModalBody>
                <Row>
                  <Col sm={12} md={12}>
                    <Label>
                      {t("calendar.modals.createCalendarModal.title")}{" "}
                      <img
                        src={asterik}
                        height={10}
                        width={10}
                        className="mt-n2"
                      />
                    </Label>
                    <Field
                      type={"text"}
                      placeholder={"Title"}
                      className={"form-control"}
                      name={"title"}
                      required
                    />
                    <ErrorMessage name={`title`}>
                      {(msg) => <span style={{ color: "red" }}>{msg}</span>}
                    </ErrorMessage>
                  </Col>
                </Row>
              </ModalBody>
              <ModalFooter>
                <Button
                  type={"submit"}
                  color="primary"
                  disabled={calendarSelector.isLoading}
                >
                  {calendarSelector.isLoading ? (
                    <Loader />
                  ) : (
                    <>{t("buttons.save")}</>
                  )}
                </Button>{" "}
                <Button
                  type={"button"}
                  color="secondary"
                  onClick={toggleNewCalendarModal}
                >
                  {t("buttons.cancel")}
                </Button>
              </ModalFooter>
            </fieldset>
          </Form>
        </Formik>
      </Modal>
    );
  };

  const handleSelectCalendarChange = (e, id) => {
    let array = selectCalendarIDS;

    array.indexOf(id) === -1
      ? array.push(id)
      : array.splice(array.indexOf(id), 1);

    dispatch(getCalendarEvents(array));
  };

  const handleCalendarLogout = () => {
    setIsLoading(true);
    const token = localStorage.getItem("token");
    return axios
      .get(`${baseURL}api/google-calendar/logout`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })
      .then((response) => {
        setIsLoading(false);
        fetchUserProfile();
      });
  };

  // const handleShareCalendarClick = (e, calendarID) => {
  //   setShareCalendarID(calendarID);
  //   setShareCalendarModal(true);
  // };

  const handleUserSelectChange = (e) => {
    let user_pr_id = [];
    e.map((item) => {
      user_pr_id.push(item.value);
    });
    setShareCalendarUserIds(user_pr_id);
  };

  const handleRoleSelectChange = (e) => {
    setShareCalendarRoleValue(e.value);
  };

  const handleShareCalendarSubmit = (value) => {
    let formData = new FormData();

    formData.append("calendar_id", shareCalendarID);

    for (let i = 0; i < shareCalendarUserIds.length; i++) {
      formData.append("user_pr_id[]", shareCalendarUserIds[i]);
    }

    formData.append("role", shareCalendarRoleValue);

    dispatch(shareCalendar(formData, setShareCalendarModal, shareCalendarID));
  };

  const renderShareCalendarModal = () => {
    return (
      <Modal isOpen={shareCalendarModal} toggle={toggleShareCalendarModal}>
        <ModalHeader toggle={toggleShareCalendarModal}>
          {t(
            "calendar.modals.calendarShareWithModal.shareCalendarModal.heading"
          )}
        </ModalHeader>
        <Formik
          initialValues={{ calendar_id: "", user_pr_id: [], role: "" }}
          enableReinitialize
          onSubmit={(values) => handleShareCalendarSubmit(values)}
        >
          <Form>
            <fieldset disabled={calendarSelector.isLoading}>
              <ModalBody>
                <Row>
                  <Col sm={12} md={12}>
                    <Label>
                      {t(
                        "calendar.modals.calendarShareWithModal.shareCalendarModal.user"
                      )}{" "}
                      <img
                        src={asterik}
                        height={10}
                        width={10}
                        className="mt-n2"
                      />
                    </Label>
                    <Select
                      isMulti
                      options={shareCalendarUserOptions}
                      closeMenuOnSelect={false}
                      onChange={(e) => handleUserSelectChange(e)}
                    />
                  </Col>
                </Row>
                <Row>
                  <Col sm={12} md={12}>
                    <Label>
                      {t(
                        "calendar.modals.calendarShareWithModal.shareCalendarModal.role"
                      )}{" "}
                      <img
                        src={asterik}
                        height={10}
                        width={10}
                        className="mt-n2"
                      />
                    </Label>
                    <Select
                      options={[
                        {
                          value: "freeBusyReader",
                          label: "See only free/busy (hide details)",
                        },
                        {
                          value: "reader",
                          label: "See all event details",
                        },
                        {
                          value: "writer",
                          label: "Make changes to events",
                        },
                      ]}
                      onChange={(e) => handleRoleSelectChange(e)}
                    />
                  </Col>
                </Row>
              </ModalBody>
              <ModalFooter>
                <Button
                  type={"submit"}
                  color="primary"
                  disabled={calendarSelector.isLoading}
                >
                  {calendarSelector.isLoading ? (
                    <Loader />
                  ) : (
                    <>{t("buttons.share")}</>
                  )}
                </Button>{" "}
                <Button
                  type={"button"}
                  color="secondary"
                  onClick={toggleShareCalendarModal}
                >
                  {t("buttons.cancel")}
                </Button>
              </ModalFooter>
            </fieldset>
          </Form>
        </Formik>
      </Modal>
    );
  };

  const handleCalendarSettings = (calendarID) => {
    dispatch(shareCalendarWith(calendarID));
    setShareCalendarID(calendarID);
    setShareCalendarSettingsModal(true);
  };

  const handleDeleteCalendarUser = (e, calendarID, userPrID) => {
    const confirm = window.confirm("Are You Sure?");
    if (confirm === true) {
      dispatch(deleteShareCalendarWith(calendarID, userPrID));
    }
  };

  const renderShareCalendarSettingsModal = () => {
    return (
      <Modal
        isOpen={shareCalendarSettingsModal}
        toggle={toggleShareCalendarSettingsModal}
      >
        <ModalHeader toggle={toggleShareCalendarSettingsModal}>
          {/* Share with specific people */}
          {t("calendar.modals.calendarShareWithModal.heading")}
        </ModalHeader>
        <ModalBody>
          <Row className={"p-2"} id={"share-with-specific-people"}>
            <Col sm={12} md={12} lg={12}>
              {!calendarSelector.isLoading ? (
                <>
                  {calendarSelector.share_calendar_users &&
                  calendarSelector.share_calendar_users.users?.length > 0
                    ? calendarSelector.share_calendar_users.users?.map(
                        (calendarUsers, index) => (
                          <div
                            key={index}
                            className={
                              "w-100 d-flex flex-row justify-content-around pt-2 pb-2"
                            }
                            id={"shared-peoples-list"}
                          >
                            <div>
                              <h6 className={"mb-0"}>
                                {calendarUsers.user.name}
                              </h6>
                              <span>{calendarUsers.email}</span>
                            </div>
                            <div>
                              <p>{calendarUsers.pivot.role}</p>
                            </div>
                            <div>
                              <button
                                type="button"
                                className="btn btn-icon btn-sm js-sweetalert"
                                title="Delete"
                                data-type="confirm"
                                onClick={(e) =>
                                  handleDeleteCalendarUser(
                                    e,
                                    calendarUsers.pivot.google_calendar_id,
                                    calendarUsers.pivot.user_pr_id
                                  )
                                }
                                style={{
                                  fontSize: `${
                                    settingsSelector.FontSize === "Large"
                                      ? "large"
                                      : settingsSelector.FontSize ===
                                        "Extra Large"
                                      ? "x-large"
                                      : "14px"
                                  }`,
                                }}
                              >
                                <i className="fa fa-trash-o text-danger" />
                              </button>
                            </div>
                          </div>
                        )
                      )
                    : "No User Found"}
                </>
              ) : (
                <DataLoader />
              )}
            </Col>
          </Row>
          <Row className={"mt-2 pl-3"}>
            <Button
              type={"button"}
              id={"calendar-add-people"}
              onClick={toggleShareCalendarModal}
            >
              <i className="fe fe-plus mr-2" />
              {t("calendar.modals.calendarShareWithModal.addPeopleButton")}
            </Button>
          </Row>
        </ModalBody>
        <ModalFooter>
          <Button
            type={"button"}
            color="primary"
            onClick={toggleShareCalendarSettingsModal}
          >
            {t("buttons.done")}
          </Button>
        </ModalFooter>
      </Modal>
    );
  };

  return (
    <div>
      <Container fluid={true} className={"mr-0"} style={{ width: "97%" }}>
        {userProfileSelector !== undefined &&
        userProfileSelector.google_calendar_access_token === false ? (
          <Row>
            <Col
              sm={12}
              md={12}
              className={"d-flex justify-content-center mt-3"}
            >
              <Button
                type={"button"}
                className={"pl-5 pr-5 pt-1 pb-1"}
                style={{ background: "#fff", color: "#000" }}
              >
                <a
                  href={`${baseURL}google/calendar/oauth`}
                  className={"d-flex align-items-center"}
                >
                  {" "}
                  <FcGoogle size={30} />{" "}
                  <p className={"ml-3 pt-2"}>
                    {/*Sisenen*/}
                    {t("calendar.googleSignInButton")}
                  </p>
                </a>
              </Button>
            </Col>
          </Row>
        ) : (
          <>
            {renderEventModal()}
            {renderCreateNewCalendarModal()}
            {renderShareCalendarModal()}
            {renderShareCalendarSettingsModal()}
            <Row className={"mt-2"}>
              <Col lg={12} md={12} sm={12}>
                <Card>
                  <div className={""}>
                    <button
                      type={"button"}
                      className={"close pr-4 pt-2"}
                      onClick={toggleCalendar}
                    />
                  </div>
                  <CardBody>
                    <Row>
                      <Col lg={3} md={3} sm={12}>
                        <div
                          className={"custom-calendar-create-event-div"}
                          onClick={handleCalendarLogout}
                        >
                          <Button className={"text-dark"}>
                            {isLoading ? (
                              <CalendarLoader />
                            ) : (
                              <>
                                <MdOutlineLogout size={14} className={"mr-2"} />
                                {t("calendar.buttons.logout")}
                              </>
                            )}
                          </Button>
                        </div>
                        <div
                          className={"custom-calendar-create-event-div"}
                          onClick={() => setModal(true)}
                        >
                          <Button className={"text-dark"}>
                            <i className="fe fe-plus mr-2" />
                            {t("calendar.buttons.createNewEvent")}
                          </Button>
                        </div>
                        <div>
                          <Calendar onChange={onChange} value={value} />
                        </div>
                        <div className={"my-calendar-collapse-wrapper mt-2"}>
                          <div className={"w-100 d-flex justify-content-end"}>
                            <Button
                              type={"button"}
                              color={"primary"}
                              onClick={() => setNewCalendarModal(true)}
                            >
                              {t("calendar.buttons.createNewCalendar")}
                            </Button>
                          </div>
                          <Accordion flush open={open} toggle={toggle}>
                            <AccordionItem>
                              <AccordionHeader targetId="1">
                                {t("calendar.accordion.myCalendar")}
                              </AccordionHeader>
                              <AccordionBody accordionId="1">
                                {!calendarSelector.isLoading ? (
                                  <div
                                    style={{
                                      overflow: "auto",
                                      height: "300px",
                                    }}
                                  >
                                    {calendarSelector.calendars.data &&
                                    calendarSelector.calendars.data.personal
                                      .length > 0
                                      ? calendarSelector.calendars.data.personal.map(
                                          (item, index) => (
                                            <div
                                              key={index}
                                              className={
                                                "w-100 d-flex flex-row justify-content-between"
                                              }
                                            >
                                              <div>
                                                <input
                                                  type={"checkbox"}
                                                  className={
                                                    "custom-input mr-3"
                                                  }
                                                  value={item.id}
                                                  defaultChecked={
                                                    item.is_checked === 1
                                                      ? true
                                                      : false
                                                  }
                                                  onChange={(e) => {
                                                    handleSelectCalendarChange(
                                                      e,
                                                      item.id
                                                    );
                                                  }}
                                                />
                                                <span>{item.title}</span>
                                              </div>
                                              <div>
                                                <button
                                                  type="button"
                                                  className="btn btn-icon btn-sm js-sweetalert"
                                                  title="Share with specific people"
                                                  onClick={() =>
                                                    handleCalendarSettings(
                                                      item.id
                                                    )
                                                  }
                                                  style={{
                                                    fontSize: `${
                                                      settingsSelector.FontSize ===
                                                      "Large"
                                                        ? "large"
                                                        : settingsSelector.FontSize ===
                                                          "Extra Large"
                                                        ? "x-large"
                                                        : "14px"
                                                    }`,
                                                  }}
                                                >
                                                  <GrUserSettings />
                                                </button>
                                                <button
                                                  type="button"
                                                  className="btn btn-icon btn-sm js-sweetalert"
                                                  title="Delete"
                                                  data-type="confirm"
                                                  onClick={(event) =>
                                                    handleDeleteCalendar(
                                                      event,
                                                      item.id
                                                    )
                                                  }
                                                  style={{
                                                    fontSize: `${
                                                      settingsSelector.FontSize ===
                                                      "Large"
                                                        ? "large"
                                                        : settingsSelector.FontSize ===
                                                          "Extra Large"
                                                        ? "x-large"
                                                        : "14px"
                                                    }`,
                                                  }}
                                                >
                                                  <i className="fa fa-trash-o text-danger" />
                                                </button>
                                              </div>
                                            </div>
                                          )
                                        )
                                      : "No Calendar Found"}
                                  </div>
                                ) : (
                                  <DataLoader />
                                )}
                              </AccordionBody>
                            </AccordionItem>
                            <AccordionItem>
                              <AccordionHeader targetId="2">
                                {t("calendar.accordion.otherCalendar")}
                              </AccordionHeader>
                              <AccordionBody accordionId="2">
                                {!calendarSelector.isLoading ? (
                                  <div
                                    style={{
                                      overflow: "auto",
                                      height: "300px",
                                    }}
                                  >
                                    {calendarSelector.calendars.data &&
                                    calendarSelector.calendars.data.other
                                      .length > 0
                                      ? calendarSelector.calendars.data.other.map(
                                          (item, index) => (
                                            <div
                                              key={index}
                                              className={
                                                "w-100 d-flex flex-row justify-content-between"
                                              }
                                            >
                                              <div>
                                                <input
                                                  type={"checkbox"}
                                                  className={
                                                    "custom-input mr-3"
                                                  }
                                                  value={item.id}
                                                  defaultChecked={
                                                    item.is_checked === 1
                                                      ? true
                                                      : false
                                                  }
                                                  onChange={(e) => {
                                                    handleSelectCalendarChange(
                                                      e,
                                                      item.id
                                                    );
                                                  }}
                                                />
                                                <span>{item.title}</span>
                                              </div>
                                            </div>
                                          )
                                        )
                                      : "No Shared Calendar Found"}
                                  </div>
                                ) : (
                                  <DataLoader />
                                )}
                              </AccordionBody>
                            </AccordionItem>
                          </Accordion>
                        </div>
                      </Col>
                      <Col lg={9} md={9} sm={12}>
                        {eventModal ? (
                          <EventOverviewModal
                            isOpen={eventModal}
                            toggle={() => {
                              setEventModal(!eventModal);
                            }}
                            event={selectedEvent}
                            handleEdit={handleEditButton}
                            handleDelete={handleDeleteEvent}
                          />
                        ) : (
                          ""
                        )}
                        <FullCalendar
                          plugins={[dayGridPlugin, interactionPlugin]}
                          initialView="dayGridMonth"
                          weekends={true}
                          dateClick={handleDateClick}
                          eventClick={handleEventClick}
                          events={calendarSelector.events}
                          eventTimeFormat={{
                            hour: "2-digit",
                            minute: "2-digit",
                            meridiem: true,
                          }}
                          displayEventEnd={false}
                        />
                      </Col>
                    </Row>
                  </CardBody>
                </Card>
              </Col>
            </Row>
          </>
        )}
      </Container>
      <Toast />
    </div>
  );
};

export default GoogleCalendar;
