import {Alert, Container} from "reactstrap";

export const SuccessMessage = ({message}) => {
    return (
        <Container fluid>
            <Alert color="success">
                {message}
            </Alert>
        </Container>
    )
}