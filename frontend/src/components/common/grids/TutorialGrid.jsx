import { GridItem, Grid } from "@chakra-ui/react";
import TutorialCard from "../cards/TutorialCard";

function TutorialGrid() {
    return (
        <Grid w={"100%"} templateColumns={"1fr"}>
            <GridItem>
                <TutorialCard />
            </GridItem>
        </Grid>
    );
}

export default TutorialGrid;
