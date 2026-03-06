import Image from "next/image"
import Link from "next/link"
import { Navigation } from "@/components/navigation"
import { Footer } from "@/components/footer"
import { Button } from "@/components/ui/button"
import { ArrowLeft, ArrowRight, Users, Clock, Moon, Sparkles, ChefHat } from "lucide-react"

const tables = [
  { id: 1, name: "Chef's View", seats: 2, description: "Front-row seats to our open kitchen" },
  { id: 2, name: "Window Table", seats: 4, description: "Elegant seating with street views" },
  { id: 3, name: "Banquette", seats: 6, description: "Comfortable booth seating in our main area" },
  { id: 4, name: "Fireplace", seats: 4, description: "Cozy setting near our marble fireplace" },
  { id: 5, name: "Private Alcove", seats: 8, description: "Semi-private space for larger parties" },
  { id: 6, name: "Chandelier", seats: 4, description: "Centered beneath our signature crystal chandelier" },
]

export default function DiningRoomPage() {
  return (
    <main className="min-h-screen">
      <Navigation />
      
      {/* Hero */}
      <section className="relative h-[60vh] min-h-[400px] flex items-end">
        <Image
          src="/images/zone-dining.jpg"
          alt="Main Dining Room at Eudaimonia"
          fill
          className="object-cover"
          priority
        />
        <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent" />
        <div className="relative z-10 container mx-auto px-4 pb-12">
          <Link href="/dining-zones" className="inline-flex items-center text-white/80 hover:text-white text-sm mb-4 transition-colors">
            <ArrowLeft className="h-4 w-4 mr-2" />
            All Dining Zones
          </Link>
          <p className="text-white/80 text-sm uppercase tracking-[0.2em] mb-2 font-medium">
            Timeless Sophistication
          </p>
          <h1 className="font-serif text-4xl md:text-5xl lg:text-6xl text-white">
            Main Dining Room
          </h1>
        </div>
      </section>

      {/* Overview */}
      <section className="py-16 md:py-24 bg-background">
        <div className="container mx-auto px-4">
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <div className="lg:col-span-2 space-y-6">
              <h2 className="font-serif text-2xl md:text-3xl text-foreground">
                The Heart of Eudaimonia
              </h2>
              <div className="space-y-4 text-muted-foreground leading-relaxed">
                <p>
                  Our Main Dining Room is where the Eudaimonia experience reaches its fullest expression. This magnificent space, anchored by soaring ceilings and illuminated by bespoke crystal chandeliers, sets the stage for extraordinary culinary journeys.
                </p>
                <p>
                  The room's design balances grandeur with intimacy. Rich wood paneling, sumptuous velvet seating, and carefully curated artwork create an atmosphere that feels both celebratory and comfortable. Tables are thoughtfully spaced to ensure privacy while maintaining the energy of a world-class dining destination.
                </p>
                <p>
                  Our open kitchen concept allows guests to witness Chef Marchetti and her team in action—a ballet of precision and creativity that adds another dimension to the dining experience.
                </p>
              </div>
            </div>

            <div className="bg-muted p-8 rounded-sm space-y-6">
              <h3 className="font-serif text-xl text-foreground">Details</h3>
              <div className="space-y-4">
                <div className="flex items-start gap-3">
                  <Users className="h-5 w-5 text-primary mt-0.5" />
                  <div>
                    <p className="font-medium text-foreground">Capacity</p>
                    <p className="text-sm text-muted-foreground">80 guests maximum</p>
                  </div>
                </div>
                <div className="flex items-start gap-3">
                  <Clock className="h-5 w-5 text-primary mt-0.5" />
                  <div>
                    <p className="font-medium text-foreground">Hours</p>
                    <p className="text-sm text-muted-foreground">Tue-Thu: 5-10 PM, Fri-Sat: 5-11 PM</p>
                  </div>
                </div>
                <div className="flex items-start gap-3">
                  <Moon className="h-5 w-5 text-primary mt-0.5" />
                  <div>
                    <p className="font-medium text-foreground">Service</p>
                    <p className="text-sm text-muted-foreground">Dinner service nightly</p>
                  </div>
                </div>
                <div className="flex items-start gap-3">
                  <ChefHat className="h-5 w-5 text-primary mt-0.5" />
                  <div>
                    <p className="font-medium text-foreground">Kitchen</p>
                    <p className="text-sm text-muted-foreground">Open kitchen concept</p>
                  </div>
                </div>
                <div className="flex items-start gap-3">
                  <Sparkles className="h-5 w-5 text-primary mt-0.5" />
                  <div>
                    <p className="font-medium text-foreground">Ambiance</p>
                    <p className="text-sm text-muted-foreground">Crystal chandeliers, wood paneling</p>
                  </div>
                </div>
              </div>
              <Link href="/book" className="block">
                <Button className="w-full bg-primary text-primary-foreground hover:bg-primary/90">
                  Reserve a Table
                  <ArrowRight className="ml-2 h-4 w-4" />
                </Button>
              </Link>
            </div>
          </div>
        </div>
      </section>

      {/* Tables */}
      <section className="py-16 md:py-24 bg-muted">
        <div className="container mx-auto px-4">
          <div className="max-w-3xl mx-auto text-center mb-12">
            <p className="text-primary text-sm uppercase tracking-[0.2em] mb-4 font-medium">
              Seating Options
            </p>
            <h2 className="font-serif text-2xl md:text-3xl text-foreground">
              Choose Your Perfect Table
            </h2>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-5xl mx-auto">
            {tables.map((table) => (
              <div key={table.id} className="bg-card p-6 rounded-sm border border-border">
                <div className="flex items-center justify-between mb-3">
                  <h3 className="font-serif text-lg text-foreground">{table.name}</h3>
                  <span className="text-sm text-muted-foreground">{table.seats} seats</span>
                </div>
                <p className="text-sm text-muted-foreground">{table.description}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="py-16 md:py-24 bg-primary text-primary-foreground">
        <div className="container mx-auto px-4 text-center">
          <h2 className="font-serif text-3xl md:text-4xl mb-6">
            Reserve the Main Dining Room
          </h2>
          <p className="text-primary-foreground/80 text-lg max-w-xl mx-auto mb-10">
            Experience the full grandeur of Eudaimonia in our signature dining space.
          </p>
          <Link href="/book">
            <Button size="lg" variant="outline" className="border-primary-foreground text-primary-foreground hover:bg-primary-foreground hover:text-primary px-8">
              Make a Reservation
              <ArrowRight className="ml-2 h-4 w-4" />
            </Button>
          </Link>
        </div>
      </section>

      <Footer />
    </main>
  )
}
