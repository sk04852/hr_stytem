import React, { useEffect, useRef, useState } from "react";
import { Alert, Card, CardBody, Col, Container, List, Row } from "reactstrap";

export const UndefinedError = () => {
  return (
    <Row className={"w-100"}>
      <Col sm={12} md={12}>
        <Card>
          <CardBody>Something Went Wrong Please Try Again Later.</CardBody>
        </Card>
      </Col>
    </Row>
  );
};

export const FormValidationErrors = ({ errors }) => {
  const [showErrorMessages, setShowErrorMessages] = useState([]);

  const scrollIntoView = () => {
    let elem = document.getElementById("error-messages");
    elem?.scrollIntoView({ behavior: "smooth" });
  };

  useEffect(() => {
    let showErrors = [];
    for (const [key, value] of Object.entries(errors)) {
      showErrors.push({
        // key: key,
        message: value[0],
      });
    }
    setShowErrorMessages(showErrors);
    scrollIntoView();
  }, [errors]);

  return (
    <Container fluid id={"error-messages"}>
      <Alert color="danger">
        <List>
          {showErrorMessages
            ? showErrorMessages.map((item, index) => (
                <li key={index}>{`${item.message}`}</li>
              ))
            : null}
        </List>
      </Alert>
    </Container>
  );
};

export const SubmitFormValidationErrors = ({ formErrors }) => {
  const [showErrorMessages, setShowErrorMessages] = useState([]);

  const scrollIntoView = () => {
    let elem = document.getElementById("error-messages");
    elem?.scrollIntoView({ behavior: "smooth" });
  };

  useEffect(() => {
    let showErrors = [];
    for (const [key, value] of Object.entries(formErrors)) {
      showErrors.push({
        key: key,
        message: value,
      });
    }
    setShowErrorMessages(showErrors);
    scrollIntoView();
  }, [formErrors]);

  return (
    <Container fluid id={"error-messages"}>
      <Alert color="danger">
        <List>
          {showErrorMessages
            ? showErrorMessages.map((item, index) => (
                <li key={index}>{`${item.message}`}</li>
              ))
            : null}
        </List>
      </Alert>
    </Container>
  );
};
