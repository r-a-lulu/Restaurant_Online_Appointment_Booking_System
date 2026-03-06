import Link from "next/link"
import { Navigation } from "@/components/navigation"
import { Footer } from "@/components/footer"
import { Button } from "@/components/ui/button"
import { CheckCircle, Calendar, MapPin, Users, Clock, ArrowRight } from "lucide-react"

export default function ConfirmationPage() {
  // Mock reservation data - in production this would come from the database
  const reservation = {
    id: "RES-2026-0306",
    guest: "John Doe",
    partySize: 4,
    zone: "Main Dining Room",
    date: "Saturday, March 14, 2026",
    time: "7:30 PM",
    email: "john@example.com",
  }

  return (
    <main className="min-h-screen flex flex-col">
      <Navigation />
      
      <section className="flex-1 flex items-center justify-center py-32 px-4 bg-background">
        <div className="w-full max-w-lg text-center">
          <div className="inline-flex items-center justify-center w-20 h-20 bg-primary/10 rounded-full mb-8">
            <CheckCircle className="h-10 w-10 text-primary" />
          </div>

          <h1 className="font-serif text-3xl md:text-4xl text-foreground mb-4">
            Reservation Confirmed
          </h1>
          <p className="text-muted-foreground mb-8">
            Thank you for choosing Eudaimonia. Your table has been reserved.
          </p>

          <div className="bg-card p-8 rounded-sm border border-border text-left mb-8">
            <div className="flex items-center justify-between mb-6 pb-6 border-b border-border">
              <span className="text-sm text-muted-foreground">Confirmation #</span>
              <span className="font-mono font-medium text-foreground">{reservation.id}</span>
            </div>

            <div className="space-y-4">
              <div className="flex items-start gap-3">
                <Users className="h-5 w-5 text-primary mt-0.5" />
                <div>
                  <p className="font-medium text-foreground">{reservation.guest}</p>
                  <p className="text-sm text-muted-foreground">{reservation.partySize} Guests</p>
                </div>
              </div>

              <div className="flex items-start gap-3">
                <MapPin className="h-5 w-5 text-primary mt-0.5" />
                <div>
                  <p className="font-medium text-foreground">{reservation.zone}</p>
                  <p className="text-sm text-muted-foreground">Eudaimonia Restaurant</p>
                </div>
              </div>

              <div className="flex items-start gap-3">
                <Calendar className="h-5 w-5 text-primary mt-0.5" />
                <div>
                  <p className="font-medium text-foreground">{reservation.date}</p>
                </div>
              </div>

              <div className="flex items-start gap-3">
                <Clock className="h-5 w-5 text-primary mt-0.5" />
                <div>
                  <p className="font-medium text-foreground">{reservation.time}</p>
                </div>
              </div>
            </div>
          </div>

          <p className="text-sm text-muted-foreground mb-8">
            A confirmation email has been sent to <span className="text-foreground">{reservation.email}</span>
          </p>

          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <Link href="/register">
              <Button className="bg-primary text-primary-foreground hover:bg-primary/90 w-full sm:w-auto">
                Create Account to Manage
                <ArrowRight className="ml-2 h-4 w-4" />
              </Button>
            </Link>
            <Link href="/">
              <Button variant="outline" className="w-full sm:w-auto">
                Return Home
              </Button>
            </Link>
          </div>
        </div>
      </section>

      <Footer />
    </main>
  )
}
