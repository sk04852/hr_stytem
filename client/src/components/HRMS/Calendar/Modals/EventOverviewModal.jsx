import React from "react";
import {
  Col,
  Modal,
  ModalBody,
  ModalHeader,
  ModalFooter,
  Row,
  Button,
} from "reactstrap";
import { useTranslation } from "react-i18next";

export const EventOverviewModal = ({
  isOpen,
  toggle,
  event,
  handleEdit,
  handleDelete,
}) => {
  const { t } = useTranslation();

  return (
    <div>
      <Modal isOpen={isOpen} toggle={toggle}>
        <ModalHeader charcode="Y" toggle={toggle}>
          {/* Sündmuse ülevaade */}
          {t("calendar.modals.eventOverviewHeading")}
        </ModalHeader>
        <ModalBody>
          <Row>
            <Col sm={12}>
              <div>
                <span>{t("calendar.modals.createEventModal.name")}: </span>{" "}
                <span
                  className="font-weight-bolder"
                  id={"event-overview-title"}
                >
                  {event["event"]["_def"]["title"]}
                </span>
              </div>
            </Col>
            <Col sm={12}>
              <div>
                <span>
                  {t("calendar.modals.createEventModal.description")}:{" "}
                </span>{" "}
                <span
                  className="font-weight-bolder"
                  id={"event-overview-description"}
                >
                  {event["event"]["_def"]["extendedProps"]["description"]}
                </span>
              </div>
            </Col>
            <Col sm={12}>
              <div>
                <span>{t("calendar.modals.eventOverviewDate")}: </span>{" "}
                <span
                  className="font-weight-bolder"
                  id={"event-overview-start"}
                >
                  {event["event"]["_def"]["extendedProps"]["startDay"]}
                </span>{" "}
                -{" "}
                <span className="font-weight-bolder" id={"event-overview-end"}>
                  {event["event"]["_def"]["extendedProps"]["endDay"]}
                </span>
              </div>
            </Col>
          </Row>
        </ModalBody>
        <ModalFooter>
          <Button onClick={handleEdit} className={"btn btn-success"}>
            {t("buttons.edit")}
          </Button>
          <Button onClick={handleDelete} className={"btn btn-danger"}>
            {t("buttons.delete")}
          </Button>
        </ModalFooter>
      </Modal>
    </div>
  );
};
