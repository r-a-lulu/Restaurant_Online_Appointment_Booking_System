"use client"

import { useState } from "react"
import Link from "next/link"
import Image from "next/image"
import { useRouter } from "next/navigation"
import { Navigation } from "@/components/navigation"
import { Footer } from "@/components/footer"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Textarea } from "@/components/ui/textarea"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { Calendar } from "@/components/ui/calendar"
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover"
import { cn } from "@/lib/utils"
import { format, addDays } from "date-fns"
import { CalendarIcon, Users, MapPin, Clock, ArrowRight, ArrowLeft, Check } from "lucide-react"

const zones = [
  { id: "patio", name: "The Patio", image: "/images/zone-patio.jpg", capacity: "2-8 guests" },
  { id: "dining-room", name: "Main Dining Room", image: "/images/zone-dining.jpg", capacity: "2-8 guests" },
  { id: "bar", name: "The Bar", image: "/images/zone-bar.jpg", capacity: "1-6 guests" },
]

const timeSlots = [
  "5:00 PM", "5:30 PM", "6:00 PM", "6:30 PM", "7:00 PM", "7:30 PM", "8:00 PM", "8:30 PM", "9:00 PM", "9:30 PM"
]

const partySizes = ["1", "2", "3", "4", "5", "6", "7", "8"]

type BookingStep = "details" | "zone" | "datetime" | "confirm"

export default function BookPage() {
  const router = useRouter()
  const [step, setStep] = useState<BookingStep>("details")
  const [isSubmitting, setIsSubmitting] = useState(false)
  
  const [formData, setFormData] = useState({
    firstName: "",
    lastName: "",
    email: "",
    phone: "",
    partySize: "",
    zone: "",
    date: undefined as Date | undefined,
    time: "",
    specialRequests: "",
  })

  const canProceed = () => {
    switch (step) {
      case "details":
        return formData.firstName && formData.lastName && formData.email && formData.phone && formData.partySize
      case "zone":
        return formData.zone
      case "datetime":
        return formData.date && formData.time
      case "confirm":
        return true
      default:
        return false
    }
  }

  const nextStep = () => {
    const steps: BookingStep[] = ["details", "zone", "datetime", "confirm"]
    const currentIndex = steps.indexOf(step)
    if (currentIndex < steps.length - 1) {
      setStep(steps[currentIndex + 1])
    }
  }

  const prevStep = () => {
    const steps: BookingStep[] = ["details", "zone", "datetime", "confirm"]
    const currentIndex = steps.indexOf(step)
    if (currentIndex > 0) {
      setStep(steps[currentIndex - 1])
    }
  }

  const handleSubmit = async () => {
    setIsSubmitting(true)
    // Mock submission - in production this would create the reservation
    await new Promise(resolve => setTimeout(resolve, 1500))
    router.push("/book/confirmation")
  }

  const selectedZone = zones.find(z => z.id === formData.zone)

  return (
    <main className="min-h-screen flex flex-col">
      <Navigation />
      
      <section className="flex-1 py-32 px-4 bg-background">
        <div className="container mx-auto max-w-3xl">
          {/* Header */}
          <div className="text-center mb-12">
            <p className="text-primary text-sm uppercase tracking-[0.2em] mb-4 font-medium">
              Reservations
            </p>
            <h1 className="font-serif text-3xl md:text-4xl text-foreground mb-4">
              Reserve Your Table
            </h1>
            <p className="text-muted-foreground">
              Complete the steps below to secure your dining experience
            </p>
          </div>

          {/* Progress */}
          <div className="flex items-center justify-center gap-2 mb-12">
            {["details", "zone", "datetime", "confirm"].map((s, i) => (
              <div key={s} className="flex items-center">
                <div
                  className={cn(
                    "w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium transition-colors",
                    step === s
                      ? "bg-primary text-primary-foreground"
                      : ["details", "zone", "datetime", "confirm"].indexOf(step) > i
                      ? "bg-primary/20 text-primary"
                      : "bg-muted text-muted-foreground"
                  )}
                >
                  {["details", "zone", "datetime", "confirm"].indexOf(step) > i ? (
                    <Check className="h-4 w-4" />
                  ) : (
                    i + 1
                  )}
                </div>
                {i < 3 && (
                  <div className={cn(
                    "w-12 h-0.5 mx-2",
                    ["details", "zone", "datetime", "confirm"].indexOf(step) > i
                      ? "bg-primary/20"
                      : "bg-muted"
                  )} />
                )}
              </div>
            ))}
          </div>

          {/* Step Content */}
          <div className="bg-card p-8 rounded-sm border border-border">
            {/* Step 1: Guest Details */}
            {step === "details" && (
              <div className="space-y-6">
                <h2 className="font-serif text-xl text-foreground mb-6">Guest Information</h2>
                
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div className="space-y-2">
                    <Label htmlFor="firstName">First Name</Label>
                    <Input
                      id="firstName"
                      value={formData.firstName}
                      onChange={(e) => setFormData({ ...formData, firstName: e.target.value })}
                      placeholder="John"
                      className="bg-background"
                    />
                  </div>
                  <div className="space-y-2">
                    <Label htmlFor="lastName">Last Name</Label>
                    <Input
                      id="lastName"
                      value={formData.lastName}
                      onChange={(e) => setFormData({ ...formData, lastName: e.target.value })}
                      placeholder="Doe"
                      className="bg-background"
                    />
                  </div>
                </div>

                <div className="space-y-2">
                  <Label htmlFor="email">Email Address</Label>
                  <Input
                    id="email"
                    type="email"
                    value={formData.email}
                    onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                    placeholder="your@email.com"
                    className="bg-background"
                  />
                </div>

                <div className="space-y-2">
                  <Label htmlFor="phone">Phone Number</Label>
                  <Input
                    id="phone"
                    type="tel"
                    value={formData.phone}
                    onChange={(e) => setFormData({ ...formData, phone: e.target.value })}
                    placeholder="(555) 123-4567"
                    className="bg-background"
                  />
                </div>

                <div className="space-y-2">
                  <Label htmlFor="partySize">Party Size</Label>
                  <Select value={formData.partySize} onValueChange={(value) => setFormData({ ...formData, partySize: value })}>
                    <SelectTrigger className="bg-background">
                      <SelectValue placeholder="Select party size" />
                    </SelectTrigger>
                    <SelectContent>
                      {partySizes.map((size) => (
                        <SelectItem key={size} value={size}>
                          {size} {parseInt(size) === 1 ? "Guest" : "Guests"}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
              </div>
            )}

            {/* Step 2: Select Zone */}
            {step === "zone" && (
              <div className="space-y-6">
                <h2 className="font-serif text-xl text-foreground mb-6">Select Dining Zone</h2>
                
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                  {zones.map((zone) => (
                    <button
                      key={zone.id}
                      onClick={() => setFormData({ ...formData, zone: zone.id })}
                      className={cn(
                        "relative aspect-[4/5] rounded-sm overflow-hidden border-2 transition-all",
                        formData.zone === zone.id
                          ? "border-primary ring-2 ring-primary/20"
                          : "border-transparent hover:border-border"
                      )}
                    >
                      <Image
                        src={zone.image}
                        alt={zone.name}
                        fill
                        className="object-cover"
                      />
                      <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent" />
                      <div className="absolute bottom-0 left-0 right-0 p-4 text-left">
                        <h3 className="font-serif text-lg text-white">{zone.name}</h3>
                        <p className="text-white/70 text-sm">{zone.capacity}</p>
                      </div>
                      {formData.zone === zone.id && (
                        <div className="absolute top-3 right-3 w-6 h-6 bg-primary rounded-full flex items-center justify-center">
                          <Check className="h-4 w-4 text-primary-foreground" />
                        </div>
                      )}
                    </button>
                  ))}
                </div>
              </div>
            )}

            {/* Step 3: Date & Time */}
            {step === "datetime" && (
              <div className="space-y-6">
                <h2 className="font-serif text-xl text-foreground mb-6">Select Date & Time</h2>
                
                <div className="space-y-4">
                  <div className="space-y-2">
                    <Label>Date</Label>
                    <Popover>
                      <PopoverTrigger asChild>
                        <Button
                          variant="outline"
                          className={cn(
                            "w-full justify-start text-left font-normal bg-background",
                            !formData.date && "text-muted-foreground"
                          )}
                        >
                          <CalendarIcon className="mr-2 h-4 w-4" />
                          {formData.date ? format(formData.date, "EEEE, MMMM d, yyyy") : "Select a date"}
                        </Button>
                      </PopoverTrigger>
                      <PopoverContent className="w-auto p-0" align="start">
                        <Calendar
                          mode="single"
                          selected={formData.date}
                          onSelect={(date) => setFormData({ ...formData, date })}
                          disabled={(date) => date < new Date() || date > addDays(new Date(), 60)}
                          initialFocus
                        />
                      </PopoverContent>
                    </Popover>
                  </div>

                  <div className="space-y-2">
                    <Label>Time</Label>
                    <div className="grid grid-cols-5 gap-2">
                      {timeSlots.map((time) => (
                        <Button
                          key={time}
                          type="button"
                          variant={formData.time === time ? "default" : "outline"}
                          size="sm"
                          onClick={() => setFormData({ ...formData, time })}
                          className={cn(
                            formData.time === time && "bg-primary text-primary-foreground"
                          )}
                        >
                          {time}
                        </Button>
                      ))}
                    </div>
                  </div>

                  <div className="space-y-2">
                    <Label htmlFor="specialRequests">Special Requests (Optional)</Label>
                    <Textarea
                      id="specialRequests"
                      value={formData.specialRequests}
                      onChange={(e) => setFormData({ ...formData, specialRequests: e.target.value })}
                      placeholder="Allergies, special occasions, seating preferences..."
                      className="bg-background min-h-[100px]"
                    />
                  </div>
                </div>
              </div>
            )}

            {/* Step 4: Confirmation */}
            {step === "confirm" && (
              <div className="space-y-6">
                <h2 className="font-serif text-xl text-foreground mb-6">Confirm Your Reservation</h2>
                
                <div className="bg-muted p-6 rounded-sm space-y-4">
                  <div className="flex items-start gap-3">
                    <Users className="h-5 w-5 text-primary mt-0.5" />
                    <div>
                      <p className="font-medium text-foreground">{formData.firstName} {formData.lastName}</p>
                      <p className="text-sm text-muted-foreground">{formData.partySize} {parseInt(formData.partySize) === 1 ? "Guest" : "Guests"}</p>
                    </div>
                  </div>

                  <div className="flex items-start gap-3">
                    <MapPin className="h-5 w-5 text-primary mt-0.5" />
                    <div>
                      <p className="font-medium text-foreground">{selectedZone?.name}</p>
                      <p className="text-sm text-muted-foreground">Eudaimonia Restaurant</p>
                    </div>
                  </div>

                  <div className="flex items-start gap-3">
                    <CalendarIcon className="h-5 w-5 text-primary mt-0.5" />
                    <div>
                      <p className="font-medium text-foreground">
                        {formData.date ? format(formData.date, "EEEE, MMMM d, yyyy") : ""}
                      </p>
                      <p className="text-sm text-muted-foreground">{formData.time}</p>
                    </div>
                  </div>

                  {formData.specialRequests && (
                    <div className="pt-4 border-t border-border">
                      <p className="text-sm text-muted-foreground">
                        <span className="font-medium text-foreground">Special Requests:</span> {formData.specialRequests}
                      </p>
                    </div>
                  )}
                </div>

                <p className="text-sm text-muted-foreground text-center">
                  A confirmation email will be sent to {formData.email}
                </p>
              </div>
            )}

            {/* Navigation */}
            <div className="flex items-center justify-between mt-8 pt-6 border-t border-border">
              {step !== "details" ? (
                <Button variant="ghost" onClick={prevStep}>
                  <ArrowLeft className="mr-2 h-4 w-4" />
                  Back
                </Button>
              ) : (
                <div />
              )}

              {step !== "confirm" ? (
                <Button
                  onClick={nextStep}
                  disabled={!canProceed()}
                  className="bg-primary text-primary-foreground hover:bg-primary/90"
                >
                  Continue
                  <ArrowRight className="ml-2 h-4 w-4" />
                </Button>
              ) : (
                <Button
                  onClick={handleSubmit}
                  disabled={isSubmitting}
                  className="bg-primary text-primary-foreground hover:bg-primary/90"
                >
                  {isSubmitting ? "Confirming..." : "Confirm Reservation"}
                  <Check className="ml-2 h-4 w-4" />
                </Button>
              )}
            </div>
          </div>

          <p className="text-center text-sm text-muted-foreground mt-6">
            Already have an account?{" "}
            <Link href="/login" className="text-primary hover:underline">
              Sign in
            </Link>{" "}
            for faster booking
          </p>
        </div>
      </section>

      <Footer />
    </main>
  )
}
